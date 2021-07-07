import pathlib

import aiohttp_jinja2
import jinja2

from parsing.parsing import Parse

BASE_DIR = pathlib.Path(__file__).parent


async def index(request):
    aiohttp_jinja2.setup(request.app, loader=jinja2.FileSystemLoader(f'{BASE_DIR}/templates'))

    # Ссылка с установленным фильтром: Тип процедуры – Запрос цен (котировок)
    url = 'https://etp.eltox.ru/registry/procedure?type=1'

    check_data = await request.app['db'].get_one()

    if not check_data:
        # Начинаем парсить сайт
        data = Parse(url)
        data_to_base = await data.parse_page()

        for d in data_to_base:
            procedure_number = int(d['procedure_number'])
            oos_procedure_number = int(d['oos_procedure_number'])
            link_procedure = d['link_procedure']
            email = d['email']
            attachment = d['attachment']

            await request.app['db'].insert_procedures(procedure_number, oos_procedure_number, link_procedure, email)

            procedure_id = await request.app['db'].get_id(procedure_number)

            for i in attachment:
                title = i['title']
                link = i['link_to_file']

                await request.app['db'].insert_attachment(title, link, procedure_id[0])

    # Выводим таблицу базы данных на экран
    data_procedure = await request.app['db'].select_procedures()

    json_data = []
    for d in data_procedure:
        id_procedure = int(d[0])
        procedure_number = int(d[1])
        oos_procedure_number = int(d[2])
        link_procedure = d[3]
        email = d[4]

        data_attachment = await request.app['db'].select_attachment(id_procedure)

        attachment_list = []
        for a in data_attachment:
            title = a[1]
            link_to_file = a[2]
            attachment_list.append({
                'title': title,
                'link_to_file': link_to_file
            })

        json_data.append({
            'id_procedure': id_procedure,
            'procedure_number': procedure_number,
            'oos_procedure_number': oos_procedure_number,
            'link_procedure': link_procedure,
            'email': email,
            'attachment': attachment_list})

    context = {
        'content_table': json_data
    }
    response = aiohttp_jinja2.render_template('index.html', request, context)

    return response
