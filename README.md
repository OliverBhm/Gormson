<h1>Vacation overview via chatbot</h1>
<p>This will get the absences for your team and post them in a specified chat every morning at
9:50 Berline time and inform you every friday at 13:00 about who'll be absent on Saturday</p>

<h2>How to use</h2>
<h3>Fetch the repository</h3>
<p>First fetch the repo from GitHub</p>
...

<h3>Update .evn file</h3>
<p>We need to create two variables in our .env file ```TIMETAPE_API_URL``` <br/>
this will get the data we need from Timetape. To receive a .ICS file please refer to <a href="https://www.timetape.de/de/news/urlaubskalender-in-outlook-google-mac-juni-2014.html">this guide.</a> We'll also need to create a 
```WEBHOOK_URL``` variable which will post the data in the corresponding chat, using 
a Google Chat bot. To optain a webhook for your chat please follow <a href="https://developers.google.com/hangouts/chat/how-tos/webhooks">this guide.</a>
</p>
<h3>Adding a Cron Job</h3>
<p>Finally we will need to add the Cron jobs to our server. Please follow the steps outlined <a href="https://laravel.com/docs/8.x/scheduling">here.</a></p>
