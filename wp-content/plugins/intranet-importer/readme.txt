// XTEC ************INFORMACIO DE LA IMPORTACIO ************ /

============================== INFO GENERAL ============================== 

- A la BBDD es crea la taula import_intranet per tal de fer un seguiment
de la importació, tant de noticies, pàgines com de missatges. 
En el cas de que es torni a realitzar una importació el contingut existen s'actualitza.

- La importació de les intranets nomès es realitza en el cas de que existeixi la taula Users

- L'usuari ès qui decideix quines dades vol importar 

============================== TAULA NEWS ==============================

Descripció de la importació de la taula NEWS a la taula wp_post

hometext = post_content
urltitle = post_name
title    = post_title
cr_date  = post_date

Els següents paràmetres s'estableixen per defecte 

post_status = publish;
post_type   = post;
post_author = id del usuari, sino existeix l'usuari es l'admin

============================== TAULA PAGES ==============================

content = post_content
title   = post_name
title   = post_title
cr_date = post_date

Els següents paràmetres s'estableixen per defecte 

post_status = publish;
post_type   = page;
post_author = id del usuari, sino existeix l'usuari es l'admin

============================== TAULA MESSAGE ==============================

content = post_content
title   = post_name
title   = post_title

Els següents paràmetres s'estableixen per defecte

post_status = publish;
post_type   = post;
post_author = Usuari admin
post_date   = s'estableix la data de la importació 

============================== SPECIAL PAGES ==============================

page_urlname    = post_name
page_title      = page_title
page_cr_date    = post_date

Els següents camps formen el contingut de la pàgina (content): 
 - Text: format pel camp text. En el cas de trobar una imatge incrustada, aquesta
        es guarda a la mediateca i es canvia el seu src
 - Url: format per source i desc
 - Video Clip: format per url i clipId
 - Google Maps: inclou els camps latitude, longitude i zoom
 - OpenStreetMap: inclou els camps latitude, longitude i zoom
 - Heading: format per headerSize i text 
 - Quote: inclou els camps text, source i desc
 - ComputerCode: format pel camp text

Els següents paràmetres s'estableixen per defecte

post_status = publish;
post_type   = page;
post_author = Usuari admin

============================== DOCUMENTS ==============================

documentName    = post_name
documentName    = page_title
description     = post_content

Els següents paràmetres s'estableixen per defecte

post_status = publish;
post_type   = page;
post_author = Usuari admin


============================== USERS ==============================

- S'importen tots els usuaris del la taula Users que no siguin admin ni xtecadmin.
- S'importen aquells que disposin de password i que el seu email no existeixi previament a la BBDD (ha de ser unic)

uname = user_login
uname = user_nicename
email = user_email
pass  = user_pass 