import aiomysql


class Database:

    # def __init__(self, app):
    #     self.app = app

    # Иницилизация базы
    async def init_my(self, app):
        conf = app['config']['mysql']
        self.pool = await aiomysql.create_pool(
            db=conf['database'],
            host=conf['host'],
            port=conf['port'],
            user=conf['user'],
            password=conf['password'],
            loop=app.loop
        )

    # Закрытие
    async def close_my(self, app):
        app['db'].close()
        await app['db'].wait_closed()

    async def create_database(self, app):
        table_1 = '''
                CREATE TABLE IF NOT EXISTS `procedures` ( 
                `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `procedure_number` int NOT NULL,
                `oos_procedure_number` varchar(255) NOT NULL,
                `link_procedure` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL
                );
                '''

        table_2 = '''
                CREATE TABLE IF NOT EXISTS `attachment` ( 
                `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL,
                `link` text NOT NULL,
                `procedure_id` int NOT NULL,
                FOREIGN KEY (`procedure_id`) REFERENCES procedures(`id`)
                );
                '''

        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(table_1)
                await cur.execute(table_2)

    async def insert_procedures(self, procedure_number, oos_procedure_number, link_procedure, email):
        sql = 'INSERT INTO `procedures` (`procedure_number`, `oos_procedure_number`, `link_procedure`, `email`) VALUES (%s, %s, %s, %s);'

        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(sql, (procedure_number, oos_procedure_number, link_procedure, email))
                await conn.commit()

    async def insert_attachment(self, title: str, link: str, procedure_id: int):
        sql = 'INSERT INTO `attachment` (`title`, `link`, `procedure_id`) VALUES (%s, %s, %s);'

        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(sql, (title, link, procedure_id))
                await conn.commit()

    async def get_id(self, procedure_number):
        sql = f'SELECT `id` FROM `procedures` WHERE `procedure_number`={procedure_number};'

        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(sql)
                rows = await cur.fetchone()
        return rows

    async def delete_all(self):
        sql_table_1 = 'DELETE FROM `attachment`;'
        sql_table_2 = 'DELETE FROM `procedures`;'

        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(sql_table_1)
                await cur.execute(sql_table_2)
                await conn.commit()

    async def select_procedures(self):
        sql = 'SELECT * FROM `procedures`;'

        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(sql)
                rows = await cur.fetchall()
        return rows

    async def select_attachment(self, procedure_id):
        sql = f'SELECT * FROM `attachment` WHERE `procedure_id`={procedure_id};'

        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(sql)
                rows = await cur.fetchall()
            return rows

    async def get_one(self):
        sql = f'SELECT `id` FROM `procedures` WHERE `id`=1;'

        async with self.pool.acquire() as conn:
            async with conn.cursor() as cur:
                await cur.execute(sql)
                rows = await cur.fetchone()
        return rows
