const {
    Client,
    GatewayIntentBits,
    EmbedBuilder,
    ActionRowBuilder,
    ButtonBuilder,
    ButtonStyle,
    ModalBuilder,
    TextInputBuilder,
    TextInputStyle
} = require('discord.js');
require('dotenv').config();
const Database = require('better-sqlite3');
const path = require('path');

// ========== CONFIG ==========
const DISCORD_TOKEN = process.env.DISCORD_TOKEN || 'YOUR_DISCORD_BOT_TOKEN';
const INWCLOUD_API_KEY = 'inwcloud_live_6ed435f82c6d426e52a7dfa8391975e946345022';
const INWCLOUD_API_URL = 'https://api.inwcloud.shop/v1/truewallet/redeem';
const DB_PATH = process.env.DB_PATH || path.join(__dirname, '..', 'database', 'bluezygpt.db');

const PACKAGES = [
    { points: 50, price: 29 },
    { points: 100, price: 50 },
    { points: 300, price: 159 },
    { points: 500, price: 200 },
    { points: 1000, price: 459 }
];

// ========== DB ==========
function getDB() {
    const db = new Database(DB_PATH);
    db.pragma('journal_mode = WAL');
    db.exec(`CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        email TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        points INTEGER DEFAULT 20,
        free_used_today INTEGER DEFAULT 0,
        last_free_reset TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )`);
    db.exec(`CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        amount INTEGER NOT NULL,
        type TEXT NOT NULL,
        voucher_link TEXT DEFAULT NULL,
        status TEXT DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )`);
    return db;
}

// ========== CLIENT ==========
const client = new Client({
    intents: [
        GatewayIntentBits.Guilds,
        GatewayIntentBits.GuildMessages,
        GatewayIntentBits.MessageContent
    ]
});

client.on('ready', () => {
    console.log(`[BOT] Logged in as ${client.user.tag}`);
});

// ========== !shop ==========
client.on('messageCreate', async (msg) => {
    if (msg.author.bot) return;
    if (msg.content.trim().toLowerCase() !== '!shop') return;

    const fields = PACKAGES.map(p => ({
        name: `${p.points} พ้อยต์`,
        value: `${p.price} ฿`,
        inline: true
    }));

    const embed = new EmbedBuilder()
        .setTitle('BluezyGPT Shop')
        .setDescription('เลือกแพ็คเกจพ้อยต์ที่ต้องการเติม แล้วกดปุ่มสั่งซื้อด้านล่าง')
        .setColor(0x6366f1)
        .addFields(fields)
        .setFooter({ text: 'ชำระผ่านซองอั่งเปา TrueMoney' })
        .setTimestamp();

    const row = new ActionRowBuilder().addComponents(
        new ButtonBuilder()
            .setCustomId('btn_buy')
            .setLabel('สั่งซื้อ')
            .setStyle(ButtonStyle.Primary)
            .setEmoji('💎')
    );

    await msg.channel.send({ embeds: [embed], components: [row] });
});

// ========== BUTTON -> MODAL ==========
client.on('interactionCreate', async (interaction) => {
    if (!interaction.isButton()) return;
    if (interaction.customId !== 'btn_buy') return;

    const modal = new ModalBuilder()
        .setCustomId('modal_redeem')
        .setTitle('สั่งซื้อพ้อยต์');

    modal.addComponents(
        new ActionRowBuilder().addComponents(
            new TextInputBuilder()
                .setCustomId('inp_points')
                .setLabel('จำนวนพ้อยต์ (50/100/300/500/1000)')
                .setPlaceholder('เช่น 100')
                .setStyle(TextInputStyle.Short)
                .setRequired(true)
        ),
        new ActionRowBuilder().addComponents(
            new TextInputBuilder()
                .setCustomId('inp_voucher')
                .setLabel('ลิงก์ซองอั่งเปา TrueMoney')
                .setPlaceholder('https://gift.truemoney.com/campaign/?v=...')
                .setStyle(TextInputStyle.Short)
                .setRequired(true)
        ),
        new ActionRowBuilder().addComponents(
            new TextInputBuilder()
                .setCustomId('inp_username')
                .setLabel('ชื่อผู้ใช้ในเว็บไซต์')
                .setPlaceholder('username ในเว็บ')
                .setStyle(TextInputStyle.Short)
                .setRequired(true)
        )
    );

    await interaction.showModal(modal);
});

// ========== MODAL SUBMIT ==========
client.on('interactionCreate', async (interaction) => {
    if (!interaction.isModalSubmit()) return;
    if (interaction.customId !== 'modal_redeem') return;

    const pointsInput = interaction.fields.getTextInputValue('inp_points').trim();
    const voucherLink = interaction.fields.getTextInputValue('inp_voucher').trim();
    const username = interaction.fields.getTextInputValue('inp_username').trim();
    const points = parseInt(pointsInput, 10);

    const pkg = PACKAGES.find(p => p.points === points);
    if (!pkg) {
        return interaction.reply({ content: 'จำนวนพ้อยต์ไม่ถูกต้อง (50, 100, 300, 500, 1000)', ephemeral: true });
    }
    if (!voucherLink.includes('gift.truemoney.com')) {
        return interaction.reply({ content: 'ลิงก์ซองไม่ถูกต้อง', ephemeral: true });
    }

    await interaction.deferReply({ ephemeral: true });

    try {
        const resp = await fetch(INWCLOUD_API_URL, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${INWCLOUD_API_KEY}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ voucher_link: voucherLink })
        });
        const data = await resp.json();

        if (resp.ok && data.status === 'success') {
            const db = getDB();
            const user = db.prepare('SELECT id FROM users WHERE username = ?').get(username);

            if (!user) {
                db.close();
                return interaction.editReply({ content: `ไม่พบผู้ใช้ "${username}" ในระบบ` });
            }

            db.prepare('UPDATE users SET points = points + ? WHERE id = ?').run(points, user.id);
            db.prepare("INSERT INTO transactions (user_id, amount, type, voucher_link, status) VALUES (?, ?, 'redeem', ?, 'success')").run(user.id, points, voucherLink);
            db.close();

            const successEmbed = new EmbedBuilder()
                .setTitle('เติมพ้อยต์สำเร็จ')
                .setColor(0x22c55e)
                .addFields(
                    { name: 'ผู้ใช้', value: username, inline: true },
                    { name: 'พ้อยต์ที่ได้', value: `+${points}`, inline: true },
                    { name: 'ราคา', value: `${pkg.price} ฿`, inline: true }
                )
                .setTimestamp();

            return interaction.editReply({ embeds: [successEmbed] });

        } else {
            const db = getDB();
            const user = db.prepare('SELECT id FROM users WHERE username = ?').get(username);
            if (user) {
                db.prepare("INSERT INTO transactions (user_id, amount, type, voucher_link, status) VALUES (?, ?, 'redeem', ?, 'failed')").run(user.id, points, voucherLink);
            }
            db.close();
            return interaction.editReply({ content: 'ซองไม่ถูกต้องหรือถูกใช้แล้ว' });
        }

    } catch (err) {
        console.error('[REDEEM ERROR]', err);
        return interaction.editReply({ content: 'เกิดข้อผิดพลาด กรุณาลองใหม่ภายหลัง' });
    }
});

// ========== LOGIN ==========
client.login(DISCORD_TOKEN);
