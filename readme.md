# For ofd-initpro.ru

Необходимо спарсить страницу: https://etp.eltox.ru/registry/procedure

С установленным фильтром: Тип процедуры – Запрос цен (котировок)

И получить:  
>1.1.	Номерпроцедуры, вида: 2187  
1.2.	ООС номер процедуры, вида: 32110439421  
1.3.	Ссылку на страницу процедуры, пример: https://etp.eltox.ru/procedure/read/2187  
1.4.	Со страницы процедуры получить:  
1.4.1.	Email (поле Почта), например:goszakaz@tppkomi.ru  
1.4.2.	Документацию к этому аукциону, имя файла и ссылки на нее (вкладка "Документы", в карточке процедуры), пример: 

Документация_на энергосбережение и повышение энергетической эффективности.docx  
-https://storage.eltox.ru/bcacd638-36fd-4e03-a7fc-e92ff963387c/60ddb36c8178b_%D0%94%D0%BE%D0%BA%D1%83%D0%BC%D0%B5%D0%BD%D1%82%D0%B0%D1%86%D0%B8%D1%8F_%D0%BD%D0%B0%20%D1%8D%D0%BD%D0%B5%D1%80%D0%B3%D0%BE%D1%81%D0%B1%D0%B5%D1%80%D0%B5%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D0%BF%D0%BE%D0%B2%D1%8B%D1%88%D0%B5%D0%BD%D0%B8%D0%B5%20%D1%8D%D0%BD%D0%B5%D1%80%D0%B3%D0%B5%D1%82%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B9%20%D1%8D%D1%84%D1%84%D0%B5%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8.docx

Результат вывести на экран, и записать в базу.  

Предпочтительно, но не обязательно:  
>•	Язык программирования PHP  
o	использовать регулярные выражения  
•	База MySQL  
o	понимать что такое индекс в MySQL и как он работает  
o	иметь представление о команде EXPLAIN  
•	можно использовать сторонние классы, структура таблиц произвольная.  
