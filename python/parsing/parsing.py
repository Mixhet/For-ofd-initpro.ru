import re

from requests_html import AsyncHTMLSession

import urllib.parse


class Parse:

    def __init__(self, url):
        self.url = url

    async def parse_page(self):
        asession = AsyncHTMLSession()

        response = await asession.get(self.url, timeout=10)

        card_list = response.html.xpath('//td[@class="descriptTenderTd"]')

        # Парсим страницу со списком процедур
        page_list = []
        for i in card_list:
            html = i.html

            # regexp
            procedure_number = re.findall(r'(?<=№\ ).*(?=</a>)', html)[0]
            oos_procedure_number = re.findall(r'(?<=№\ ООС:\ ).*(?=</span>)', html)[0]

            links = i.absolute_links
            link_procedure = ''
            for link in links:
                link_procedure = link
                break

            page_list.append({
                'procedure_number': procedure_number,
                'oos_procedure_number': oos_procedure_number,
                'link_procedure': link_procedure})

        # Парсим страницу процедуры
        common_data = []
        for d in page_list:
            asession = AsyncHTMLSession()

            link_procedure = d['link_procedure']

            try:
                response = await asession.get(link_procedure, timeout=10)

                # Рендерим страницу
                await response.html.arender(timeout=20)
            except:
                continue

            email = response.html.xpath('//*[@id="tab-basic"]/table/tbody/tr[12]/td')[0].text

            attachment = response.html.xpath('//span[@class="qq-upload-file"]')

            attachment_list = []
            for a in attachment:
                title = a.text

                link_file =  urllib.parse.quote(list(a.absolute_links)[0]).replace('%3A', ':')
                attachment_list.append({'title': title, 'link_to_file': link_file})

            common_data.append({
                'procedure_number': d['procedure_number'],
                'oos_procedure_number': d['oos_procedure_number'],
                'link_procedure': link_procedure,
                'email': email,
                'attachment': attachment_list
            })

        return common_data
