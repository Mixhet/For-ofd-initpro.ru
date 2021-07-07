from aiohttp import web

from db.db import Database
from routes import setup_routes
from settings import config

if __name__ == '__main__':
    app = web.Application()
    app['config'] = config
    app['db'] = Database()
    setup_routes(app)
    app.on_startup.append(app['db'].init_my)
    app.on_startup.append(app['db'].create_database)
    app.on_cleanup.append(app['db'].close_my)
    web.run_app(app, host='127.0.0.1', port=5012)
