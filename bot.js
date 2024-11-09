const { Telegraf, Markup } = require('telegraf');
const { Sequelize, DataTypes } = require('sequelize');

// Підключення до бази даних
const sequelize = new Sequelize('bddvvx5uepfk7vo0almc', 'ual3odet1pi3ftpn', 'yeaGgGxFe0dCTwkhmCdZ', {
    host: 'bddvvx5uepfk7vo0almc-mysql.services.clever-cloud.com',
    dialect: 'mysql',
    port: 3306,
    logging: false, // Вимкнути SQL-логи в консолі
});

// Перевірка підключення
sequelize.authenticate()
    .then(() => console.log('Підключено до бази даних'))
    .catch((err) => console.error('Помилка підключення до БД:', err));

// Оголошення моделей
const Performance = sequelize.define('performance', {
    id: { type: DataTypes.BIGINT.UNSIGNED, primaryKey: true, autoIncrement: true },
    title: DataTypes.STRING,
    duration: DataTypes.INTEGER,
    image: DataTypes.STRING,
}, {
    timestamps: false,
});

const Genre = sequelize.define('genre', {
    id: { type: DataTypes.BIGINT.UNSIGNED, primaryKey: true, autoIncrement: true },
    name: DataTypes.STRING,
}, {
    timestamps: false,
});

const Actor = sequelize.define('actor', {
    id: { type: DataTypes.BIGINT.UNSIGNED, primaryKey: true, autoIncrement: true },
    first_name: DataTypes.STRING,
    last_name: DataTypes.STRING,
}, {
    timestamps: false,
});

// Моделі для асоціацій
const PerformanceGenre = sequelize.define('performance_genre', {
    performance_id: { type: DataTypes.BIGINT.UNSIGNED, references: { model: Performance, key: 'id' } },
    genre_id: { type: DataTypes.BIGINT.UNSIGNED, references: { model: Genre, key: 'id' } }
}, {
    timestamps: false,
});

const PerformanceActor = sequelize.define('performance_actor', {
    performance_id: { type: DataTypes.BIGINT.UNSIGNED, references: { model: Performance, key: 'id' } },
    actor_id: { type: DataTypes.BIGINT.UNSIGNED, references: { model: Actor, key: 'id' } }
}, {
    timestamps: false,
    tableName: 'performance_actor' // Вказуємо правильне ім'я таблиці
});

// Встановлення асоціацій між моделями
Performance.belongsToMany(Genre, { through: PerformanceGenre, foreignKey: 'performance_id' });
Genre.belongsToMany(Performance, { through: PerformanceGenre, foreignKey: 'genre_id' });

Performance.belongsToMany(Actor, { through: PerformanceActor, foreignKey: 'performance_id' });
Actor.belongsToMany(Performance, { through: PerformanceActor, foreignKey: 'actor_id' });

// Ініціалізація бота
const bot = new Telegraf('7355998053:AAFzF4962NSnBzkdLOkcAV1gB0nYq0u6qfk'); // Замініть на ваш токен

// Команда /start — відправляє головне меню
bot.command('start', (ctx) => {
    const menu = Markup.keyboard([
        ['🎭 Вистави', '🎬 Жанри'],
        ['👨‍🎤 Актори', '📅 Перегляд по даті'],
    ]).resize();
    ctx.reply('Вітаємо у боті для покупки театральних квитків! Виберіть одну з опцій:', menu);
});

// Команда для перегляду всіх вистав
bot.hears('🎭 Вистави', async (ctx) => {
    try {
        const performances = await Performance.findAll();
        const buttons = performances.map((perf) => 
            Markup.button.callback(perf.title, `performance_${perf.id}`)
        );
        ctx.reply('Оберіть виставу для перегляду деталей:', Markup.inlineKeyboard(buttons, { columns: 2 }));
    } catch (error) {
        console.error('Помилка:', error);
        ctx.reply('Сталася помилка при завантаженні вистав.');
    }
});

// Обробка натискання на виставу
bot.action(/performance_(\d+)/, async (ctx) => {
    const performanceId = ctx.match[1];
    try {
        // Знаходимо виставу за ID
        const performance = await Performance.findByPk(performanceId, {
            include: [Actor, Genre], // Включаємо акторів та жанри, якщо потрібно
        });

        if (!performance) {
            return ctx.reply('Виставу не знайдено.');
        }

        let message = `🎭 ${performance.title}\n`;
        message += `⏳ Тривалість: ${performance.duration} хв\n`;
        
        // Якщо є актори
        if (performance.actors.length > 0) {
            message += '\nАктори:\n';
            performance.actors.forEach((actor) => {
                message += `${actor.first_name} ${actor.last_name}\n`;
            });
        } else {
            message += '\nНемає акторів для цієї вистави.\n';
        }

        // Якщо є жанри
        if (performance.genres.length > 0) {
            message += '\nЖанри:\n';
            performance.genres.forEach((genre) => {
                message += `${genre.name}\n`;
            });
        } else {
            message += '\nНемає жанрів для цієї вистави.\n';
        }

        ctx.reply(message);
    } catch (error) {
        console.error('Помилка:', error);
        ctx.reply('Сталася помилка при завантаженні вистави.');
    }
});

// Команда для фільтрації вистав за жанром
bot.hears('🎬 Жанри', async (ctx) => {
    try {
        const genres = await Genre.findAll();
        const buttons = genres.map((g) => Markup.button.callback(g.name, `genre_${g.id}`));
        console.log(genres);  // Логування жанрів
        ctx.reply('Оберіть жанр:', Markup.inlineKeyboard(buttons, { columns: 2 }));
    } catch (error) {
        console.error('Помилка:', error);
        ctx.reply('Сталася помилка при завантаженні жанрів.');
    }
});

// Обробка вибору жанру
bot.action(/genre_(\d+)/, async (ctx) => {
    const genreId = ctx.match[1];
    try {
        const genre = await Genre.findByPk(genreId);
        if (!genre) {
            return ctx.reply('Жанр не знайдений.');
        }

        const performances = await Performance.findAll({
            include: {
                model: Genre,
                where: { id: genreId },
            },
        });

        let message = `Вистави жанру: ${genre.name}\n\n`;
        performances.forEach((p) => {
            message += `🎭 ${p.title}\n`;
        });

        ctx.reply(message);
    } catch (error) {
        console.error('Помилка:', error);
        ctx.reply('Сталася помилка при завантаженні вистав за жанром.');
    }
});

// Команда для перегляду акторів
bot.hears('👨‍🎤 Актори', async (ctx) => {
    try {
        const actors = await Actor.findAll();
        const buttons = actors.map((actor) => Markup.button.callback(`${actor.first_name} ${actor.last_name}`, `actor_${actor.id}`));
        ctx.reply('Оберіть актора:', Markup.inlineKeyboard(buttons, { columns: 2 }));
    } catch (error) {
        console.error('Помилка:', error);
        ctx.reply('Сталася помилка при завантаженні акторів.');
    }
});

// Обробка натискання на актора
bot.action(/actor_(\d+)/, async (ctx) => {
    const actorId = ctx.match[1];
    try {
        // Знаходимо актора за id
        const actor = await Actor.findByPk(actorId, {
            include: {
                model: Performance,
                through: { attributes: [] }, // Ігноруємо проміжну таблицю
            },
        });

        if (!actor) {
            return ctx.reply('Актор не знайдений.');
        }

        let message = `${actor.first_name} ${actor.last_name} бере участь у таких виставах:\n\n`;

        // Якщо актор має вистави, то виводимо їх
        if (actor.performances.length > 0) {
            actor.performances.forEach((performance) => {
                message += `🎭 ${performance.title} (тривалість: ${performance.duration} хв)\n`;
            });
        } else {
            message += 'Актор не бере участь в жодній виставі.\n';
        }

        ctx.reply(message);
    } catch (error) {
        console.error('Помилка:', error);
        ctx.reply('Сталася помилка при завантаженні вистав за актором.');
    }
});

// Запуск бота
bot.launch()
    .then(() => console.log('Бот запущений'))
    .catch((err) => console.error('Помилка запуску бота:', err));
