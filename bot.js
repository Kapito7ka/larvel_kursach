const { Telegraf, Markup } = require('telegraf');
const { Sequelize, DataTypes } = require('sequelize');

const sequelize = new Sequelize('bddvvx5uepfk7vo0almc', 'ual3odet1pi3ftpn', 'yeaGgGxFe0dCTwkhmCdZ', {
    host: 'bddvvx5uepfk7vo0almc-mysql.services.clever-cloud.com',
    dialect: 'mysql',
    port: 3306,
    logging: false, 
});


sequelize.authenticate()
    .then(() => console.log('Підключено до бази даних'))
    .catch((err) => console.error('Помилка підключення до БД:', err));

// Оголошення моделей
const Performance = sequelize.define('performance', {
    id: { type: DataTypes.BIGINT.UNSIGNED, primaryKey: true, autoIncrement: true },
    title: DataTypes.STRING,
    description: DataTypes.TEXT, 
    duration: DataTypes.INTEGER,
    image: DataTypes.STRING,
}, {
    timestamps: false,
});

const Producer = sequelize.define('producer', {
    id: { type: DataTypes.BIGINT.UNSIGNED, primaryKey: true, autoIncrement: true },
    first_name: DataTypes.STRING,
    last_name: DataTypes.STRING,
}, {
    timestamps: false,
});

const User = sequelize.define('User', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    name: { type: DataTypes.STRING, allowNull: false },
    email: { type: DataTypes.STRING, allowNull: false, unique: true },
    email_verified_at: { type: DataTypes.DATE, allowNull: true },
    password: { type: DataTypes.STRING, allowNull: false },
    remember_token: { type: DataTypes.STRING, allowNull: true },
    created_at: { type: DataTypes.DATE, allowNull: false, defaultValue: DataTypes.NOW },
    updated_at: { type: DataTypes.DATE, allowNull: false, defaultValue: DataTypes.NOW },
    age: { type: DataTypes.INTEGER, allowNull: true },
    phone_numbers: { type: DataTypes.STRING, allowNull: false }, 
    status: { type: DataTypes.STRING, allowNull: true }
}, {
    tableName: 'users', 
    timestamps: false   
});

const Ticket = sequelize.define('Ticket', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    ticket_number: { type: DataTypes.STRING, allowNull: false },
    date: { type: DataTypes.DATE, allowNull: false },
    time: { type: DataTypes.TIME, allowNull: false },
    show_id: { type: DataTypes.INTEGER, allowNull: false },
    seat_id: { type: DataTypes.STRING, allowNull: false },
    user_id: { type: DataTypes.INTEGER, allowNull: false },
    price: { type: DataTypes.FLOAT, allowNull: false },
    discount_id: { type: DataTypes.INTEGER, allowNull: true },
    created_at: { type: DataTypes.DATE, allowNull: false, defaultValue: DataTypes.NOW },
    updated_at: { type: DataTypes.DATE, allowNull: false, defaultValue: DataTypes.NOW },
}, {
    tableName: 'tickets', 
    timestamps: false,   
});

const Hall = sequelize.define('hall', {
    id: { type: DataTypes.BIGINT.UNSIGNED, primaryKey: true, autoIncrement: true },
    hall_number: DataTypes.INTEGER,
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

const Show = sequelize.define('show', {
    id: { type: DataTypes.BIGINT.UNSIGNED, primaryKey: true, autoIncrement: true },
    performance_id: { type: DataTypes.BIGINT.UNSIGNED, references: { model: Performance, key: 'id' } },
    datetime: DataTypes.DATE,
    price: DataTypes.FLOAT,
    hall_id: { type: DataTypes.BIGINT.UNSIGNED, references: { model: Hall, key: 'id' } },
    created_at: {
        type: DataTypes.DATE,
        field: 'created_at',
    },
    updated_at: {
        type: DataTypes.DATE,
        field: 'updated_at',
    },
}, {
    timestamps: true,
    createdAt: 'created_at',
    updatedAt: 'updated_at',
});

const PerformanceActor = sequelize.define('performance_actor', {
    performance_id: { type: DataTypes.BIGINT.UNSIGNED, references: { model: Performance, key: 'id' } },
    actor_id: { type: DataTypes.BIGINT.UNSIGNED, references: { model: Actor, key: 'id' } }
}, {
    timestamps: false,
    tableName: 'performance_actor' 
});
const PerformanceProducer = sequelize.define('performance_producer', {
    performance_id: { type: DataTypes.BIGINT.UNSIGNED, references: { model: Performance, key: 'id' } },
    producer_id: { type: DataTypes.BIGINT.UNSIGNED, references: { model: Producer, key: 'id' } }
}, {
    timestamps: false,
});

Performance.belongsToMany(Producer, { through: PerformanceProducer, foreignKey: 'performance_id' });
Producer.belongsToMany(Performance, { through: PerformanceProducer, foreignKey: 'producer_id' });

User.hasMany(Ticket, { foreignKey: 'user_id' });
Ticket.belongsTo(User, { foreignKey: 'user_id' });

Performance.belongsToMany(Genre, { through: PerformanceGenre, foreignKey: 'performance_id' });
Genre.belongsToMany(Performance, { through: PerformanceGenre, foreignKey: 'genre_id' });

Performance.belongsToMany(Actor, { through: PerformanceActor, foreignKey: 'performance_id' });
Actor.belongsToMany(Performance, { through: PerformanceActor, foreignKey: 'actor_id' });

Performance.hasMany(Show, { foreignKey: 'performance_id' });
Show.belongsTo(Performance, { foreignKey: 'performance_id' });

Hall.hasMany(Show, { foreignKey: 'hall_id' });
Show.belongsTo(Hall, { foreignKey: 'hall_id' });

Ticket.belongsTo(Show, { foreignKey: 'show_id' });
Show.hasMany(Ticket, { foreignKey: 'show_id' });

const bot = new Telegraf('7355998053:AAFzF4962NSnBzkdLOkcAV1gB0nYq0u6qfk'); 

bot.command('start', async (ctx) => {
    const menu = Markup.keyboard([
        ['🎭 Вистави', '🎬 Жанри'],
        ['👨‍🎤 Актори', '📅 Перегляд по даті'],
        ['🎟 Мої квитки']
    ]).resize();
    await ctx.reply('Вітаємо у боті для покупки театральних квитків! Виберіть одну з опцій:', menu);
});

bot.hears('🎟 Мої квитки', async (ctx) => {
    await ctx.reply('Будь ласка, поділіться своїм номером телефону для перевірки квитків:',
        Markup.keyboard([
            Markup.button.contactRequest('Поділитись номером')
        ]).resize().oneTime());
});

bot.on('contact', async (ctx) => {
    const phoneNumber = ctx.message.contact.phone_number;

    try {
        const tickets = await Ticket.findAll({
            include: [
                {
                    model: Show,
                    include: [Performance, Hall],
                },
                {
                    model: User,
                    where: { phone_numbers: phoneNumber },
                    attributes: [],
                },
            ],
        });

        if (tickets.length === 0) {
            return ctx.reply('У вас немає квитків.');
        }

        for (const ticket of tickets) {
            const showDate = new Date(ticket.show.datetime).toLocaleString('uk-UA');
            const message = `🎫 Ваш квиток:\n` +
                `🎭 Вистава: ${ticket.show.performance.title}\n` +
                `🕒 Дата і час: ${showDate}\n` +
                `📍 Зал: ${ticket.show.hall.hall_number || 'Невідомо'}\n` +
                `💺 Місце: Ряд №3 Місце №9 \n` +
                `🗓 Дата покупки: 13.112014`;

            // Якщо є зображення для вистави, додаємо його
            if (ticket.show.performance.image) {
                await ctx.replyWithPhoto(ticket.show.performance.image);
            }

            await ctx.reply(message);
        }
    } catch (error) {
        console.error('Помилка при завантаженні квитків:', error);
        ctx.reply('Сталася помилка при завантаженні ваших квитків.');
    }
});

bot.hears('🎭 Вистави', async (ctx) => {
    try {
        const performances = await Performance.findAll();
        const buttons = performances.map((perf) => 
            Markup.button.callback(perf.title, `performance_${perf.id}`)
        );
        await ctx.reply('Оберіть виставу для перегляду деталей:', Markup.inlineKeyboard(buttons, { columns: 2 }));
    } catch (error) {
        console.error('Помилка:', error);
        await ctx.reply('Сталася помилка при завантаженні вистав.');
    }
});

bot.action(/performance_(\d+)/, async (ctx) => {
    const performanceId = ctx.match[1]; // Отримуємо ID вистави
    try {
        const performance = await Performance.findByPk(performanceId);

        if (!performance) {
            return ctx.reply('Виставу не знайдено.');
        }

        // Вивести основну інформацію
        let message = `🎭 ${performance.title}\n`;
        message += `📝 Опис: ${performance.description ? performance.description.substring(0, 100) + '...' : 'Немає опису'}\n`;
        message += `⏳ Тривалість: ${performance.duration} хв\n`;

        const buttonMore = Markup.button.callback("Детальніше", `details_${performance.id}`);

        // Якщо є зображення для вистави, додаємо його
        if (performance.image) {
            await ctx.replyWithPhoto(performance.image);
        }

        // Відправляємо повідомлення з кнопкою "Детальніше"
        await ctx.replyWithHTML(message, Markup.inlineKeyboard([buttonMore]));

    } catch (error) {
        console.error('Помилка при завантаженні вистави:', error);
        ctx.reply('Сталася помилка при завантаженні вистави.');
    }
});

bot.action(/details_(\d+)/, async (ctx) => {
    const performanceId = ctx.match[1]; // Отримуємо ID вистави після натискання кнопки "Детальніше"
    try {
        const performance = await Performance.findByPk(performanceId, {
            include: [
                { model: Genre, through: { attributes: [] } },
                { model: Actor, through: { attributes: [] } },
                { model: Producer, through: { attributes: [] } },
            ],
        });

        if (!performance) {
            return ctx.reply('Виставу не знайдено.');
        }

        let detailedMessage = `🎭 ${performance.title}\n`;
        detailedMessage += `📝 Опис: ${performance.description || 'Немає опису'}\n`;
        detailedMessage += `🎬 Жанри: ${performance.genres.map(g => g.name).join(', ') || 'Немає'}\n`;
        detailedMessage += `👨‍🎤 Актори: ${performance.actors.map(a => `${a.first_name} ${a.last_name}`).join(', ') || 'Немає'}\n`;
        detailedMessage += `👨‍💼 Продюсери: ${performance.producers.map(p => `${p.first_name} ${p.last_name}`).join(', ') || 'Немає'}\n`;
        detailedMessage += `⏳ Тривалість: ${performance.duration} хв\n`;

        const buttonBuyTicket = Markup.button.url("Купити квиток", "https://lesyatheatre.com.ua/ukr/tickets");

        // Якщо є зображення для вистави, додаємо його
        if (performance.image) {
            await ctx.replyWithPhoto(performance.image);
        }

        // Відправляємо повідомлення з деталями вистави та кнопкою "Купити квиток"
        await ctx.replyWithHTML(detailedMessage, Markup.inlineKeyboard([buttonBuyTicket]));

    } catch (error) {
        console.error('Помилка при завантаженні деталей вистави:', error);
        ctx.reply('Сталася помилка при завантаженні деталей вистави.');
    }
});

bot.hears('🎬 Жанри', async (ctx) => {
    try {
        const genres = await Genre.findAll();
        const buttons = genres.map((g) => Markup.button.callback(g.name, `genre_${g.id}`));
        console.log(genres);  
        ctx.reply('Оберіть жанр:', Markup.inlineKeyboard(buttons, { columns: 2 }));
    } catch (error) {
        console.error('Помилка:', error);
        ctx.reply('Сталася помилка при завантаженні жанрів.');
    }
});

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

        if (performances.length > 0) {
            let message = `Вистави жанру: ${genre.name}\n\n`;
            const buttons = performances.map((p) => {
                return Markup.button.callback(p.title, `performance_${p.id}`);
            });

            ctx.reply('За цим жаром є такі вистави:', Markup.inlineKeyboard(buttons, { columns: 2 }));
        } else {
            ctx.reply(`Для жанру "${genre.name}" не знайдено жодної вистави.`);
        }
    } catch (error) {
        console.error('Помилка:', error);
        ctx.reply('Сталася помилка при завантаженні вистав за жанром.');
    }
});

bot.hears('📅 Перегляд по даті', async (ctx) => {
    try {
        const months = [
            'Січень', 'Лютий', 'Березень', 'Квітень',
            'Травень', 'Червень', 'Липень', 'Серпень',
            'Вересень', 'Жовтень', 'Листопад', 'Грудень'
        ];

        const monthButtons = months.map((month, index) =>
            Markup.button.callback(month, `month_2024_${index + 1}`)
        );

        await ctx.reply('Оберіть місяць:', Markup.inlineKeyboard(monthButtons, { columns: 3 }));
    } catch (error) {
        console.error('Помилка при обробці команди Перегляд по даті:', error);
        ctx.reply('Сталася помилка. Спробуйте пізніше.');
    }
});

bot.action(/month_2024_(\d+)/, async (ctx) => {
    const month = ctx.match[1]; 
    const year = 2024;

    try {
        const daysInMonth = new Date(year, month, 0).getDate(); 

        const dayButtons = Array.from({ length: daysInMonth }, (_, i) =>
            Markup.button.callback(`${i + 1}`, `day_${year}_${month}_${i + 1}`)
        );

        await ctx.reply(`Оберіть день для ${new Date(year, month - 1).toLocaleString('uk-UA', { month: 'long' })}:`, 
            Markup.inlineKeyboard(dayButtons, { columns: 7 })
        );
    } catch (error) {
        console.error('Помилка при створенні списку днів:', error);
        ctx.reply('Сталася помилка при виборі днів.');
    }
});

bot.action(/day_(\d+)_(\d+)_(\d+)/, async (ctx) => {
    const [year, month, day] = ctx.match.slice(1, 4);

    try {
        const selectedDate = new Date(year, month - 1, day);
        const shows = await Show.findAll({
            where: {
                datetime: {
                    [Sequelize.Op.gte]: selectedDate,
                    [Sequelize.Op.lt]: new Date(selectedDate.getTime() + 24 * 60 * 60 * 1000),
                },
            },
            include: [Performance, Hall],
        });

        if (shows.length === 0) {
            return ctx.reply(`На ${selectedDate.toLocaleDateString('uk-UA')} немає доступних вистав.`);
        }

        for (const show of shows) {
            const performance = show.performance;
            const showTime = new Date(show.datetime).toLocaleTimeString('uk-UA');
            const message = `🎭 *${performance.title}*\n` +
                            `🕒 Час: ${showTime}\n` +
                            `💰 Ціна: ${show.price} грн\n` +
                            `📍 Зал: ${show.hall ? show.hall.hall_number : 'невідомо'}\n`;

            const button = Markup.inlineKeyboard([
                Markup.button.callback('Детальніше', `details_${show.id}`)
            ]);

            if (performance.image) {
                await ctx.replyWithPhoto(performance.image, {
                    caption: message,
                    parse_mode: 'Markdown',
                    ...button,
                });
            } else {
                await ctx.reply(message, {
                    parse_mode: 'Markdown',
                    ...button,
                });
            }
        }
    } catch (error) {
        console.error('Помилка при обробці вибраної дати:', error);
        ctx.reply('Сталася помилка при завантаженні вистав на вибрану дату.');
    }
});

bot.action(/details_(\d+)/, async (ctx) => {
    const showId = ctx.match[1];

    try {
        const show = await Show.findByPk(showId, {
            include: [Performance, Hall]
        });

        if (!show) {
            return ctx.reply('Не вдалося знайти інформацію про цю виставу.');
        }

        const performance = show.performance;
        const detailsMessage = `🎭 *${performance.title}* - Детальна інформація\n\n` +
                               `📝 Опис: ${performance.description || 'Опис відсутній'}\n` +
                               `🕒 Дата та час: ${new Date(show.datetime).toLocaleString('uk-UA')}\n` +
                               `📍 Зал: ${show.hall ? show.hall.hall_number : 'невідомо'}\n` +
                               `💰 Ціна: ${show.price} грн`;

        await ctx.reply(detailsMessage, { parse_mode: 'Markdown' });
    } catch (error) {
        console.error('Помилка при отриманні деталей вистави:', error);
        ctx.reply('Сталася помилка при завантаженні деталей вистави.');
    }
});


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

bot.action(/actor_(\d+)/, async (ctx) => {
    const actorId = ctx.match[1];
    try {
        const actor = await Actor.findByPk(actorId, {
            include: {
                model: Performance,
                through: { attributes: [] }, 
            },
        });

        if (!actor) {
            return ctx.reply('Актор не знайдений.');
        }

        let message = `${actor.first_name} ${actor.last_name} бере участь у таких виставах:\n\n`;

        // Для кожної вистави актора створюємо острівець
        for (const performance of actor.performances) {
            message += `🎭 *${performance.title}* \n` +
                       `⏳ Тривалість: ${performance.duration} хв\n`;

            const button = Markup.inlineKeyboard([
                Markup.button.callback('Детальніше', `details_${performance.id}`)
            ]);

            if (performance.image) {
                // Якщо вистава має фотографію
                await ctx.replyWithPhoto(performance.image, {
                    caption: message,
                    parse_mode: 'Markdown',
                    ...button
                });
            } else {
                // Якщо фотографії немає
                await ctx.reply(message, {
                    parse_mode: 'Markdown',
                    ...button
                });
            }

            // Очищаємо повідомлення для наступної вистави
            message = '';
        }

    } catch (error) {
        console.error('Помилка:', error);
        ctx.reply('Сталася помилка при завантаженні вистав за актором.');
    }
});

// Обробник кнопки "Детальніше"
bot.action(/details_(\d+)/, async (ctx) => {
    const performanceId = ctx.match[1];

    try {
        const performance = await Performance.findByPk(performanceId, {
            include: [Show] // Додаємо шоу, щоб вивести деталі
        });

        if (!performance) {
            return ctx.reply('Не вдалося знайти виставу.');
        }

        const detailsMessage = `🎭 *${performance.title}* - Детальна інформація\n\n` +
                               `📝 Опис: ${performance.description || 'Опис відсутній'}\n` +
                               `⏳ Тривалість: ${performance.duration} хв\n` +
                               `🕒 Дата та час: ${performance.shows[0] ? new Date(performance.shows[0].datetime).toLocaleString('uk-UA') : 'невідомо'}\n`;
                            
        await ctx.reply(detailsMessage, { parse_mode: 'Markdown' });
    } catch (error) {
        console.error('Помилка при отриманні деталей вистави:', error);
        ctx.reply('Сталася помилка при завантаженні деталей вистави.');
    }
});

bot.launch()
    .then(() => console.log('Бот запущений'))
    .catch((err) => console.error('Помилка запуску бота:', err));
