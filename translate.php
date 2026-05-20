<?php

session_start();

$application_name = "Logos";

if (empty($_SESSION["language"])) {
    $language = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2);
    $recognized_languages = ["en", "it", "zh"];
    $language = in_array($language, $recognized_languages) ? $language : "en";
    $_SESSION["language"] = $language;
}

$languagechange = $_GET["language"];
$urlchange = $_GET["url"];

if (!empty($languagechange) && !empty($urlchange)) {
    $_SESSION["language"] = $languagechange;
    header("Location: " . $urlchange);
}
if ($_SESSION["language"] === "en") {
    
    $notifications_paragraph = "<u>Advice</u>: download the mobile app of your email service (Gmail, Outlook, Yahoo...) to remain updated on new " .$application_name ." messages. <br>If emails end up in <u>spam</u>, add " .$application_name ." to reliable senders.";
    $allow_notifications = "Allow notifications";
    $settings_and_other = "Settings and More";
    $settings_and_participants = "Settings and Participants";
    $online_notification_warning = "No notification is going to be sent while you are online.";
    $send_notifications_group = "Receive email notifications from group conversations";
    $send_notifications_private = "Receive email notifications from private conversations";
    $manage_notifications = "Get notifications";
    $image_prev = "image";
    $video_prev = "video";
    $new_message_from = "New message from " .$application_name;
    $confirm = "Confirm";
    $send_notifications_to = "Send notifications to ";
    $welcome = "Welcome to " .$application_name;
    $bad_request = "Bad Request";
    $bad_request_error = "Bad request error";
    $bad_request_error_message =
        "Your browser probably accessed the site incorrectly.";
    $unauthorized = "Unauthorized";
    $unauthorized_error = "Unauthorized error";
    $unauthorized_error_message =
        "The server could not authenticate your request.";
    $forbidden = "Forbidden";
    $forbidden_error = "Forbidden error";
    $forbidden_error_message =
        "The server understood your request, but refused to authorize it.";
    $not_found = "Page not Found";
    $not_found_error = "Missing Page";
    $not_found_error_message =
        "The requested page could not be found on the site.";
    $internal_server = "Internal Server Error";
    $internal_server_error = "Internal Server Error";
    $internal_server_error_message =
        "The server could not handle your request.";
    $js_warning =
        $application_name .
        " doesn't work properly without JavaScript, please enable it.";
    $go_home = "Go home";
    $activate_title = "Account Activation";
    $unsuccessful_activation = "Unsuccessful Account Activation";
    $unsuccessful_activation_message =
        "Unfortunately, your account could not be activated.";
    $no_sending_first = "Can't send messages to ";
    $no_sending_second = ", you've been blocked.";
    $chat_with = "Chat with";
    $contacts = "Your contacts ";
    $groups = "Your groups ";
    $settings = "Settings ";
    $logout = "Logout ";
    $home = "Home";
    $not_saved = "is not between your contacts.";
    $block = "Block user";
    $or = "or";
    $save = "save";
    $blocked_notice = "You blocked this contact.";
    $unblock = "Unblock?";
    $send = "send";
    $read_message = "seen";
    $not_read = "not seen";
    $groups_in_common = "Groups in Common";
    $no_groups_in_common = "No groups in common.";
    $contact_settings = "Settings";
    $modify_contact_name = "Modify contact name";
    $confirm_edits = "Confirm edits";
    $delete_contact = "Delete contact";
    $clear_chat = "Clear chat";
    $delete_chat = "Delete chat";
    $no_contact_found = "no contact found";
    $no_chat_found = "no chat found";
    $add_contact = "Add Contact";
    $enter_contact_code = "contact's code";
    $enter_contact_name = "contact's name";
    $search_contact = "Search Contact";
    $dear = "Dear";
    $welcome_to = "Welcome to";
    $registration_email_first =
        ", the instant messaging platform designed to be secure and efficient.";
    $registration_email_second =
        "Kindly click on this link to activate your account.";
    $successful_account_creation_message =
        "Your account was created successfully. Please check your email inbox";
    $unsuccessful_account_creation = "Unfortunately, your account could not be created. Possible issues: email address or personal code already taken, server error.
    
               You can still ";
    $unsuccessful_account_creation_two =
        "The emails don't match. You can still ";
    $unsuccessful_account_creation_three =
        "You were detected as a bot. You can still ";
    $try_again = "try again";
    $account_deletion = "Account Deletion";
    $account_deletion_page = "Account Deletion";
    $account_deletion_message = "Are you sure you want to delete your account? This operation
               is not reversible and is going to delete all of your messages and media sent, group participations and contacts. Also, people who engaged chats with you are going to lose them";
    $account_deletion_confirm = "I understand and agree to proceed.";
    $enter_messaging_code = "Enter your messaging code";
    $delete_account = "Delete Account";
    $group_participants = "Participants";
    $add_member = "Add Member";
    $remove_member = "Remove Member";
    $remove_admin_title = "Remove Admin Title";
    $add_admin_title = "Add Admin Title";
    $member_code = "member's code";
    $group_settings = "Settings";
    $modify_group_name = "Modify group name";
    $select_group_PFP = "Select new group profile picture";
    $delete_group = "Delete group";
    $leave_group = "Leave group";
    $no_group_found = "no group found";
    $add_group = "Add Group";
    $enter_new_group_name = "group's name";
    $search_group = "Search group";
    $enter_group_name = "Enter group name";
    $logos_info =
        $application_name . " is a private instant messaging platform.";
    $messaging_code_statement = "is your unique messaging code";
    $messages = "Messages";
    $chat = "chat";
    $no_chats_yet = "No chats yet.";
    $no_messages_yet = "No messages yet.";
    $wrong_credentials =
        "Wrong credentials, not verified account or blocked from " .
        $application_name .
        ". Try again.";
    $access = "Log-in";
    $enter_your_email = "Enter your email";
    $enter_your_credentials = "Enter your credentials.";
    $complete_register_form = "Enter your personal information to sign up to " .$application_name .".";
    $enter_your_password = "Enter your password";
    $enter_personal_code = "Enter personal code";
    $or_generate =
        "or <input type='checkbox' id='generate' name='generate'> <label for='generate'>generate it</label>";
    $enter_your_name = "Enter your name and surname";
    $no_account = "Don't have an account?";
    $forgot_password = "Forgot password?";
    $password_error = "Could not update your password. Please try again.";
    $recover_your = "Recover Your " . $application_name . " Account";
    $create_new_account = "Create a new strong password.";
    $recover = "Recover";
    $account_recovery_successful =
        "Account recovery request created successfully. Please check your email inbox";
    $recovery_email_first =
        "Our systems received an account recovery request. If it wasn't you, feel free to ignore this email. Otherwise,";
    $recovery_email_second = "click on this link to reset your password.";
    $account_recovery_error =
        "Could not create account recovery request. Try again.";
    $no_email_found =
        "Unfortunately, we could not found an account connected to the provided email. Please try again.";
    $enter_email_to_find = "Enter your email to find account.";
    $register_to = "Register to";
    $enter_name_surname = "Enter name and surname";
    $enter_email = "Enter email";
    $enter_new_password = "Enter new password";
    $create_account = "Create Account";
    $already_have_account = "Already have an account?";
    $successful_password_modified = "Password modified successfully.";
    $passwords_no_match = "New passwords don't match.";
    $account_settings = "Settings";
    $new_name = "Enter new name";
    $select_PFP = "Select new profile picture";
    $delete_PFP = "Delete profile picture";
    $change_password = "Change password";
    $new_password = "new password";
    $new_password_confirm = "confirm new password";
    $new_group = "New Group";
    $contact_successful = "Contact created.";
    $group_successful = "Group created.";
    $blocking_notice = "Member has blocked you.";
    $recover_title = "Recover Account";
    $new_account_title = "Register";
    $search_chat = "search contacts, groups or someone's code";
    $terms_notice = "I agree to the <a href='terms.php'>Terms & Conditions</a>";
    $terms_title = "Terms & Conditions";
    $terms_message = "<p style='font-size:20px; text-align:left;' class='lead'>By creating an account on " .$application_name .", you confirm that you have read the privacy policy, <a href='privacy.php'>readable here</a>, that you are at least 13, and agree to use our services responsibly, without: <br><br>
           - Offending other users of the platform.<br>
           - Inciting hatred, violence or disseminate pornography.<br>
           - Sending spam messages.</br>
           - Uploading content (photos, audios, videos, images) of other individuals to the platform without their explicit consent.</br> <br> When the terms and conditions and the privacy policy are changed, we are going to write it on our website and send you an email or possibly send you a notification on the mobile application. </p>";

    $privacy_title = "Privacy Policy";
    $privacy_message = "<p style = 'font-size: 20px; text-align: left; ' class = 'lead'> This privacy policy is about how the data you provide is used.
             We collect and store only and exclusively fundamental information, that is: <br> <br>
             - When creating your account: your name and surname, your email and your new password. These personal information must be correct and up-to-date.<br>
             - Your profile picture. <br>
             - The text and / or content (photos, audio, video, images) of the messages you send in private or group conversations. <br>
             - The contacts you created. <br>
             - Contacts blocked by you. <br> <br>
   
             In the mobile application, a notification is sent when a message is sent, which can be deactivated in the preferences.<br><br>
   
             Sensitive data sent by the user is strongly encrypted and stored in the data centers of Aruba Business S.R.L. These are absolutely not shared, but could be provided to the authorities if needed.<br><br>
   
             We do not use cookies, except to store your session when you log-in to the site.<br><br>
   
             When you delete your account, all your data is immediately deleted forever and is not recoverable in any way, the same applies to the deletion of messages. </p>";

    $terms_conjunction =
        "Read this document in conjunction with the <a href='terms.php'>terms and conditions</a>.";
    $show_hide = "Show / Hide";
    $missing_chat_title = "Chat not found";
    $missing_chat =
        "Messaging code not found. <a href='home.php'>Go home</a>.";
    $search_again = "Search again";
    $search_button = "Search";
    $biography_thrust = "Write a short biography about yourself.";
    $group_biography_thrust = "Describe briefly the group.";
    $code_warning = "<br>→ code you will be reached at<br> → only numbers, letters and spaces.";
    $media_selected = "Media selected, now press \"send\".";
    $enter_new_code = "Enter new code";
    $change = "Change code";
    $banned_warning = "You have been banned from using " .$application_name .". Reason: ";
    $banned_title = "You have been blocked";
    $email_changed = "Email changed. Check your inbox or spam.";
    $change_email =
        "If you haven't received any email, look in the <b>spam folder</b> or check that";
    $change_email_two =
        "is your correct email. In case it is not, enter account's password and a new email:";
    $account_password = "Account's password";
    $new_email = "New Email";
    $confirm_email = "Confirm your Email";
    $done_warning =
        "When you're done, <a style='color:blue;' href='index.php'>log-in</a>.";
    $message_deleted = "[message deleted]";
    $private_account = "Private account (you will have to accept new  conversations)";
    $accept_chat = "Accept this chat";
    $disaccept_chat = "Reject this chat";
    $view_media_notice = "View media";
    $you = "You";
    $code_or_password_wrong = "Your password or code is not correct.";
    $adminnotice = "Administr.";
    
} else if ($_SESSION["language"] === "it") {
    
    $notifications_paragraph = "<u>Consiglio</u>: scarica l'applicazione da telefono del tuo servizio e-mail (Gmail, Outlook, Yahoo...) per rimanere aggiornato sui nuovi messaggi da " .$application_name .". <br> Se le email finiscono nella <u>posta indesiderata</u>, aggiungi " .$application_name ." ai mittenti attendibili.";
    $allow_notifications = "Consenti notifiche";
    $settings_and_other = "Impostazioni e Altro";
    $settings_and_participants = "Impostazioni e Partecipanti";
    $online_notification_warning = "Nessuna notifica verrà inviata mentre sei online.";
    $send_notifications_group = "Ricevi notifiche e-mail dalle conversazioni di gruppo";
    $send_notifications_private = "Ricevi notifiche e-mail dalle conversazioni private";
    $manage_notifications = "Ottieni notifiche";
    $image_prev = "immagine";
    $video_prev = "video";
    $new_message_from = "Nuovo messaggio da " .$application_name;
    $confirm = "Conferma";
    $send_notifications_to = "Invia notifiche a ";
    $welcome = "Benvenuti su " .$application_name;
    $bad_request = "Bad Request";
    $bad_request_error = "Errore bad request";
    $bad_request_error_message =
        "Il tuo browser ha effettuato l'accesso al sito in maniera errata.";
    $unauthorized = "Unauthorized";
    $unauthorized_error = "Errore unauthorized";
    $unauthorized_error_message =
        "Il server non ha potuto autenticare la tua richiesta.";
    $forbidden = "Forbidden";
    $forbidden_error = "Errore forbidden";
    $forbidden_error_message =
        "Il server ha compreso la tua richiesta, ma si è rifiutato di autorizzarla.";
    $not_found = "Pagina non Trovata";
    $not_found_error = "Pagina Mancante";
    $not_found_error_message =
        "La pagina richiesta non è stata trovata nel sito.";
    $internal_server = "Internal Server Error";
    $internal_server_error = "Errore Internal Server";
    $internal_server_error_message =
        "Il server non ha potuto gestire la tua richiesta.";
    $js_warning =
        $application_name .
        " non funziona al meglio senza JavaScript, pertanto ti è richiesto di attivarlo.";
    $go_home = "Vai alla home";
    $activate_title = "Attivazione Account";
    $unsuccessful_activation = "Attivazione Account non Riuscita";
    $unsuccessful_activation_message =
        "Putroppo, non è stato possibile attivare il tuo account.";
    $no_sending_first = "Non puoi mandare messaggi a ";
    $no_sending_second = ", sei stato bloccato";
    $chat_with = "Messaggia con";
    $contacts = "I tuoi contatti ";
    $groups = "I tuoi gruppi ";
    $settings = "Preferenze ";
    $logout = "Esci ";
    $home = "Home ";
    $not_saved = "non è fra i tuoi contatti.";
    $block = "Bloccalo";
    $or = "o";
    $save = "salvalo";
    $blocked_notice = "Hai bloccato questo contatto.";
    $unblock = "Vuoi sbloccarlo?";
    $send = "invia";
    $read_message = "letto";
    $not_read = "non letto";
    $groups_in_common = "Gruppi in Comune";
    $no_groups_in_common = "Nessun gruppo in comune.";
    $contact_settings = "Impostazioni";
    $modify_contact_name = "Cambia nome del contatto";
    $confirm_edits = "Conferma le modifiche";
    $delete_contact = "Elimina contatto";
    $clear_chat = "Svuota conversazione";
    $delete_chat = "Elimina conversazione";
    $no_contact_found = "nessun contatto trovato";
    $no_chat_found = "nessuna conversazione trovata";
    $add_contact = "Nuovo contatto";
    $enter_contact_code = "codice del contatto";
    $enter_contact_name = "nome del contatto";
    $search_contact = "Cerca un contatto";
    $dear = "Caro";
    $welcome_to = "Benvenuto a";
    $registration_email_first =
        ", la piattaforma di messagistica instantanea progettata per essere sicura ed efficiente.";
    $registration_email_second =
        "Per favore clicca sul link per attivare il tuo account.";
    $successful_account_creation_message =
        "Il tuo account è stato creato con successo. Per favore controlla la tua posta in arrivo";
    $unsuccessful_account_creation = "Purtroppo, non è stato possibile creare il tuo account. Errori possibili: indirizzo email o codice personale già in uso, errore del server.
           
               Puoi sempre ";
    $unsuccessful_account_creation_two =
        "Le email non combaciano. Puoi sempre ";
    $unsuccessful_account_creation_three =
        "Sei stato rilevato come bot. Puoi sempre ";
    $try_again = "riprovare";
    $account_deletion = "Eliminazione dell'Account";
    $account_deletion_page = "Eliminazione dell'Account";
    $account_deletion_message =
        "Sei sicuro di voler eliminare il tuo account? Questa operazione non è reverisibile ed eliminerà tutti i tuoi messaggi e media inviati, partecipazione ai gruppi e contatti. Inoltre, gli utenti che hanno intrapreso conversazioni con te le perderanno.";
    $account_deletion_confirm = "Comprendo e accetto di proseguire.";
    $enter_messaging_code = "Immetti il tuo codice di messaggistica";
    $delete_account = "Elimina account";
    $group_participants = "Partecipanti";
    $add_member = "Nuovo Membro";
    $remove_member = "Rimuovi Membro";
    $remove_admin_title = "Rimuovi Titolo di Admin";
    $add_admin_title = "Aggiungi Titolo di Admin";
    $member_code = "codice del membro";
    $group_settings = "Impostazioni";
    $modify_group_name = "Cambia nome del gruppo";
    $select_group_PFP = "Seleziona una nuova foto profilo del gruppo";
    $delete_group = "Elimina il gruppo";
    $leave_group = "Lascia il gruppo";
    $no_group_found = "nessun gruppo trovato";
    $add_group = "Nuovo gruppo";
    $enter_new_group_name = "nome del gruppo";
    $search_group = "Cerca un gruppo";
    $logos_info =
        $application_name . " è una piattaforma di messaggistica instantanea.";
    $messaging_code_statement = "è il tuo codice di messaggistica univoco";
    $messages = "Messaggi";
    $chat = "vai";
    $no_chats_yet = "Ancora nessuna conversazione.";
    $no_messages_yet = "Ancora nessun messaggio.";
    $wrong_credentials =
        "Credenziali errate, account non verificato o bloccato da " . $application_name . ". Riprova.";
    $access = "Entra";
    $enter_your_email = "Immetti la tua email";
    $enter_your_credentials = "Immetti le tue credenziali.";
    $complete_register_form = "Immetti le tue informazioni personali per iscriverti a " .$application_name .".";
    $enter_your_password = "Immetti la tua password";
    $enter_personal_code = "Immetti nuovo codice personale";
    $or_generate =
        "oppure <input type='checkbox' id='generate' name='generate'> <label for='generate'>generalo</label>";
    $enter_your_name = "Immetti il tuo nome e cognome";
    $no_account = "Non hai un'account?";
    $forgot_password = "Hai dimenticato la password?";
    $password_error =
        "Non è stato possibile aggiornare la tua password. Per favore riprova.";
    $recover_your = "Recupera il Tuo Account " . $application_name;
    $create_new_account = "Crea una nuova password sicura.";
    $recover = "Recupera";
    $account_recovery_successful =
        "Richiesta di recupero account creata con successo. Per favore controlla la tua posta in arrivo";
    $recovery_email_first =
        "I nostri sistemi hanno ricevuto una richiesta di recupero account. Se non eri tu, sentiti libero di ignorare questa email. Altrimenti,";
    $recovery_email_second =
        "clicca su questo link per ripristinare la password.";
    $account_recovery_error =
        "Non è stato possibile creare la richiesta di recupero account. Riprova.";
    $no_email_found =
        "Putroppo, non è stato possibile trovare un account connesso a questa email. Per favore riprova.";
    $enter_email_to_find = "Immetti la tua email per trovare il tuo account.";
    $register_to = "Registrati a";
    $enter_name_surname = "Immetti il tuo nome e cognome.";
    $enter_email = "Immetti email";
    $enter_new_password = "Immetti una nuova password";
    $create_account = "Crea Account";
    $already_have_account = "Hai già un account?";
    $successful_password_modified = "Password modificata con successo.";
    $passwords_no_match = "Le nuove password non combaciano.";
    $account_settings = "Preferenze";
    $new_name = "Immetti un nuovo nome";
    $select_PFP = "Seleziona una nuova foto profilo";
    $delete_PFP = "Elimina foto profilo";
    $change_password = "Cambia password";
    $new_password = "nuova password";
    $new_password_confirm = "conferma la password";
    $new_group = "Nuovo gruppo";
    $contact_successful = "Contatto creato.";
    $group_successful = "Gruppo creato.";
    $blocking_notice = "L'utente ti ha bloccato.";
    $recover_title = "Recupera Account" ;
    $new_account_title = "Registrati";
    $search_chat = "cerca contatti, gruppi o il codice di qualcuno";
    $terms_notice = "Accetto i <a href='terms.php'>Termini e le Condizioni</a>";
    $terms_title = "Termini e Condizioni";
    $terms_message = "<p style='font-size:20px; text-align:left;' class ='lead'>Creando un account su " .$application_name .", confermi di aver letto l'informativa sulla privacy, <a href='privacy.php'>leggibile qui</a>, di avere almeno 13 anni, e accetti di utilizzare i nostri servizi in modo responsabile, senza: <br><br>
           - Offendere altri utenti della piattaforma.<br>
           - Incitare l'odio, la violenza o diffondere materiale pornografico.<br>
           - Inviare messaggi di spam.</br>
           - Caricare contenuti (foto, audio, video, immagini) di altri individui sulla piattaforma senza il loro esplicito consenso.
   
          </br><br> Quando i termini e le condizioni e la politica sulla riservatezza verranno modificati, lo scriveremo sul nostro sito web e ti invieremo una email ed eventualmente una notifica sull’applicazione da cellulare.<br><br>
          
          <b>I termini e le condizioni sono forniti in lingua italiana, ma in caso di controversie fa fede la versione inglese</b>.
         </p>";

    $privacy_title = "Informativa sulla Privacy";

    $privacy_message = "<p style='font-size:20px; text-align:left;' class='lead'>Questa politica sulla riservatezza definisce come vengono usati i dati da te forniti.
           Raccogliamo e stocchiamo solo ed esclusivamente informazioni fondamentali, ovvero: <br><br>
           - Alla creazione del tuo account: il tuo nome e cognome, la tua email e la tua nuova password.<br>
           - La tua foto profilo.<br>
           - Il testo e/o i contenuti (foto, audio, video, immagini) dei messaggi che invii nelle conversazioni private o dei gruppi.<br>
           - I contatti da te creati.<br>
           - I contatti da te bloccati.<br><br>
   
           Nell'applicazione da cellulare, riceverai una notifica quando ti viene inviato un messaggio, impostazione disattivabile nelle preferenze.<br><br>
   
           I dati sensibili inviati dall’utente sono fortemente crittografati e stoccati nei data center di Aruba Business S.R.L. Questi non vengono assolutamente condivisi, ma potrebbero essere forniti alle autorità in caso di necessità. <br><br>
   
           Non facciamo uso di cookie, se non per stoccare la tua sessione quando effettui il log-in al sito. <br><br>
   
           Quando cancelli il tuo account, tutti i tuoi dati vengono immediatamente eliminati per sempre e non sono recuperabili in nessun modo, lo stesso si applica per la cancellazione di messaggi.<br><br>
           
           <b>I termini e le condizioni sono forniti in lingua italiana, ma in caso di controversie fa fede la versione inglese</b>.</p>";

    $terms_conjunction =
        "Leggi questo documento insieme ai <a href='terms.php'>termini e le condizioni</a>.";
    $show_hide = "Mostra / Nascondi";
    $missing_chat_title = "Conversazione non trovata";
    $missing_chat =
        "Codice di messaggistica non trovato. <a href='home.php'>Vai alla home</a>.";
    $search_again = "Cerca di nuovo";
    $search_button = "Cerca";
    $biography = "Biografia";
    $biography_thrust = "Scrivi una breve biografia su di te.";
    $group_biography_thrust = "Descrivi brevemente il gruppo.";
    $code_warning = "<br>→ codice a cui gli utenti ti scriveranno<br> → solo lettere, numeri e spazi permessi.";
    $media_selected = "Media selezionato, adesso premi \"invia\".";
    $enter_new_code = "Immetti nuovo codice";
    $change = "Cambia codice";
    $banned_warning = "Sei stato bloccato dall'usare " .$application_name .". Motivo: ";
    $banned_title = "Sei stato bloccato";
    $email_changed = "E-mail cambiata. Controlla la tua posta in arrivo o spam";
    $change_email =
        "Se non hai ricevuto alcuna e-mail, guarda nella <b>cartella dello spam</b> o controlla che";
    $change_email_two =
        "sia la tua e-mail corretta. Se non lo è, immetti la password dell'account ed una nuova email:";
    $account_password = "Password dell'Account";
    $new_email = "Nuova E-mail";
    $confirm_email = "Conferma la tua Email";
    $done_warning =
        "Fatto ciò, esegui il <a style='color:blue;' href='index.php'>log-in</a>.";
    $message_deleted = "[messaggio eliminato]";
    $private_account = "Account privato (dovrai accettare le nuove conversazioni)";
    $accept_chat = "Accetta questa conversazione";
    $disaccept_chat = "Rifiuta questa conversazione";
    $view_media_notice = "Visiona media";
    $you = "Tu";
    $code_or_password_wrong = "La tua password o il tuo codice è errato.";
    $adminnotice = "Amministr.";
    
} else if ($_SESSION["language"] === "zh") {

    $notifications_paragraph = "<u>建议</u>：下载您的电子邮件服务（QQ, 网易...）的移动应用程序，以随时了解新的 " .$application_name ." 消息。如果消息以 <u>垃圾邮件</u> 结尾，请添加" .$application_name ."到受信任的发件人。";
    $allow_notifications = "您同意通知";
    $settings_and_other = "设置等";
    $settings_and_participants = "设置和与会者";
    $online_notification_warning = "当您在线时，不会发送任何通知。";
    $send_notifications_group = "接收来自群组对话的电子邮件通知";
    $send_notifications_private = "接收来自私人对话的电子邮件通知";
    $manage_notifications = "收到通知";
    $image_prev = "图片";
    $video_prev = "视频";
    $new_message_from = "来自的新消息 " .$application_name;
    $confirm = "确认";
    $send_notifications_to = "发送通知到 ";
    $welcome = "欢迎来到 " .$application_name;
    $bad_request = "Bad Request";
    $bad_request_error = "Bad request 网页找不到";
    $bad_request_error_message =
        "您的浏览器可能不正确地访问了该站点。";
    $unauthorized = "Unauthorized";
    $unauthorized_error = "Unauthorized 网页找不到";
    $unauthorized_error_message =
        "服务器无法验证您的请求。";
    $forbidden = "Forbidden";
    $forbidden_error = "Forbidden 网页找不到";
    $forbidden_error_message =
        "服务器理解您的请求,但拒绝授权。";
    $not_found = "页面未找到";
    $not_found_error = "缺页";
    $not_found_error_message =
        "无法在网站上找到请求的页面。";
    $internal_server = "Internal Server Error";
    $internal_server_error = "Internal Server 网页找不到";
    $internal_server_error_message =
        "服务器无法处理您的请求。";
    $js_warning = "没有JavaScript, " .$application_name ."无法正常工作,请启用它。";
    $go_home = "回家";
    $activate_title = "账户激活";
    $unsuccessful_activation = "帐户激活";
    $unsuccessful_activation_message =
        "很遗憾,您的帐户无法激活。";
    $no_sending_first = "无法向发送消息 ";
    $no_sending_second = "，你被屏蔽了。";
    $chat_with = "聊聊";
    $contacts = "您的联系人 ";
    $groups = "您的群组 ";
    $settings = "设置 ";
    $logout = "注销 ";
    $home = "首页";
    $not_saved = " 不是在你的联系人之间。";
    $block = "阻止用户";
    $or = "要么";
    $save = "保存";
    $blocked_notice = "您已阻止此联系人。";
    $unblock = "解锁？";
    $send = "发送";
    $read_message = "看到";
    $not_read = "未见";
    $groups_in_common = "共同组";
    $no_groups_in_common = "没有共同的团体。";
    $contact_settings = "联系人设置";
    $modify_contact_name = "修改联系人姓名";
    $confirm_edits = "确认编辑";
    $delete_contact = "删除联系人";
    $clear_chat = "清除聊天";
    $delete_chat = "删除聊天";
    $no_contact_found = "未找到联系人";
    $no_chat_found = "没有找到聊天";
    $add_contact = "增加联系人";
    $enter_contact_code = "联系人代码";
    $enter_contact_name = "联系人姓名";
    $search_contact = "搜索联系人";
    $dear = "亲爱的";
    $welcome_to = "欢迎来到";
    $registration_email_first =
        "，旨在安全高效的即时通讯平台。";
    $registration_email_second =
        "请点击此链接激活您的帐户。";
    $successful_account_creation_message =
        "您的帐户已成功创建。请检查您的电子邮件收件箱";
    $unsuccessful_account_creation = "很遗憾，您的帐户无法创建。可能的问题：电子邮件地址或个人密码已被使用，服务器错误。
    
               你还可以";
    $unsuccessful_account_creation_two =
        "电子邮件不匹配。你还可以";
    $unsuccessful_account_creation_three =
        "您已被检测为机器人。你总是可以";
    $try_again = "再试一次";
    $account_deletion = "帐户删除";
    $account_deletion_page = "帐户删除";
    $account_deletion_message = "您确定要删除您的帐户吗？这个操作
               不可逆，将删除您发送的所有消息和媒体、群组参与和联系人。此外，与您聊天的人也会失去他们";
    $account_deletion_confirm = "我理解并同意继续。";
    $enter_messaging_code = "输入您的消息代码";
    $delete_account = "删除帐户";
    $group_participants = "参与者";
    $add_member = "添加会员";
    $remove_member = "删除成员";
    $remove_admin_title = "删除管理员头衔";
    $add_admin_title = "添加管理员头衔";
    $member_code = "会员代码";
    $group_settings = "设置";
    $modify_group_name = "修改组名";
    $select_group_PFP = "选择新的群组头像";
    $delete_group = "删除组";
    $leave_group = "离开组";
    $no_group_found = "未找到组";
    $add_group = "添加组";
    $enter_new_group_name = "组名";
    $search_group = "搜索组";
    $enter_group_name = "输入群组名称";
    $logos_info =
        $application_name . "是一个私人即时通讯平台。";
    $messaging_code_statement = "是您唯一的消息代码";
    $messages = "留言";
    $chat = "聊天";
    $no_chats_yet = "还没有聊天。";
    $no_messages_yet = "还没有消息。";
    $wrong_credentials =
        "错误的凭据、未验证的帐户或被" .
        $application_name .
        "。 阻止。再试一次。";
    $access = "登录";
    $enter_your_email = "输入你的电子邮箱";
    $enter_your_credentials = "输入您的凭据";
    $complete_register_form = "输入您的个人信息以注册" .$application_name ."。";
    $enter_your_password = "输入您的密码";
    $enter_personal_code = "输入个人密码";
    $or_generate =
        "要么 <input type='checkbox' id='generate' name='generate'> <label for='generate'>生成它</label>";
    $enter_your_name = "输入您的姓名和姓氏";
    $no_account = "没有帐户？";
    $forgot_password = "忘记密码？";
    $password_error = "无法更新您的密码。请再试一次。";
    $recover_your = "恢复您的 " . $application_name . " 帐户";
    $create_new_account = "创建一个新的强密码。";
    $recover = "恢复";
    $account_recovery_successful =
        "帐户恢复请求创建成功。请检查您的电子邮件收件箱";
    $recovery_email_first =
        "我们的系统收到了一个帐户恢复请求。如果不是您，请随时忽略此电子邮件。否则，";
    $recovery_email_second = "单击此链接以重置您的密码。";
    $account_recovery_error =
        "无法创建帐户恢复请求。再试一次。";
    $no_email_found =
        "不幸的是，我们找不到与提供的电子邮件相关联的帐户。请再试一次。";
    $enter_email_to_find = "输入您的电子邮件以查找帐户。";
    $register_to = "注册到";
    $enter_name_surname = "输入姓名和姓氏";
    $enter_email = "输入电子邮件";
    $enter_new_password = "输入新密码";
    $create_account = "创建帐号";
    $already_have_account = "已经有一个帐户？";
    $successful_password_modified = "密码修改成功。";
    $passwords_no_match = "新密码不匹配。";
    $account_settings = "设置";
    $new_name = "输入新名称";
    $select_PFP = "选择新的头像";
    $delete_PFP = "删除头像";
    $change_password = "更改密码";
    $new_password = "新密码";
    $new_password_confirm = "确认新密码";
    $new_group = "新集团";
    $contact_successful = "已创建联系人";
    $group_successful = "组创建。";
    $blocking_notice = "会员屏蔽了你。";
    $recover_title = "恢复账户";
    $new_account_title = "登记";
    $search_chat = "搜索联系人、群组或某人的代码";
    $terms_notice = "我同意 <a href='terms.php'>条款和条件</a>";
    $terms_title = "条款和条件";
    $terms_message = "<p style='font-size:20px; text-align:left;' class='lead'>通过在 " .$application_name ." 上创建帐户，您确认您已阅读隐私政策，<a href='privacy.php'>在这里可读</a>，您至少年满 13 岁，并同意负责任地使用我们的服务，而无需: <br><br>
           - 冒犯平台的其他用户。<br>
           - 煽动仇恨、暴力或传播色情内容。<br>
           - 发送垃圾邮件。</br>
           - 未经他人明确同意，将其他个人的内容（照片、音频、视频、图像）上传到平台。</br> <br> 当条款和条件以及隐私政策发生变化时，我们将在我们的网站上写下来并向您发送电子邮件或最终在移动应用程序上向您发送通知。<br><br><b>条款及细则以中文提供，如有争议以英文版本为准 </b>。 </p>";

    $privacy_title = "隐私政策";
    $privacy_message = "<p style = 'font-size: 20px; text-align: left; ' class = 'lead'> 本隐私政策是关于如何使用您提供的数据。

             我们仅收集和存储基本信息，即： <br> <br>
             - 创建帐户时：您的姓名、电子邮件和新密码。这些个人信息必须是正确和最新的。<br>
             - 你的头像。 <br>
             - 您在私人或群组对话中发送的消息的文本和/或内容（照片、音频、视频、图像）。 <br>
             - 您创建的联系人。 <br>
             - 被您屏蔽的联系人。 <br> <br>
   
             在移动应用程序中，发送消息时会发送通知，可以在首选项中将其禁用。<br><br>
   
             用户发送的敏感数据经过高度加密并存储在 Aruba Business S.R.L. 的数据中心。这些绝对不是共享的，但如果需要，可以提供给当局。<br><br>
   
             我们不使用 cookie，除了在您登录网站时存储您的会话。<br><br>
   
             当您删除您的帐户时，您的所有数据将立即永久删除并且无法以任何方式恢复，同样适用于删除消息。<br><br><b>条款及细则以中文提供，如有争议以英文版本为准 </b>。 </p>";

    $terms_conjunction =
        "结合 <a href='terms.php'>条款和条件</a> 阅读本文档。";
    $show_hide = "显示隐藏";
    $missing_chat_title = "未找到聊天";
    $missing_chat =
        "未找到消息代码。 <a href='home.php'>回家</a>。";
    $search_again = "再次搜索";
    $search_button = "搜索";
    $biography_thrust = "写一篇关于你自己的简短传记。";
    $group_biography_thrust = "简要描述该组。";
    $code_warning = "<br> → 代码用户会写信给你 <br> → 只允许使用字母、数字和空格。";
    $media_selected = "选择媒体，现在按“发送”。";
    $enter_new_code = "输入新代码";
    $change = "更改代码";
        "其他徒劳的搜索，您将被 " .$application_name ." 屏蔽三个小时。";
    $banned_warning = "您已被禁止使用徽标。原因：";
    $banned_title = "您已被屏蔽";
    $email_changed = "电子邮件已更改。检查您的收件箱或垃圾邮件。";
    $change_email =
        "如果您没有收到任何电子邮件，请查看<b>垃圾邮件文件夹</b>或检查";
    $change_email_two =
        "是您正确的电子邮件。如果不是，请输入帐户密码和新电子邮件：";
    $account_password = "账户密码";
    $new_email = "新邮件";
    $confirm_email = "确认您的电子邮件";
    $done_warning =
        "当你完成后， <a style='color:blue;' href='index.php'>登录</a>。";
    $message_deleted = "[消息已删除]";
    $private_account = "私人账户（你必须接受新的对话）";
    $accept_chat = "接受此聊天";
    $disaccept_chat = "拒绝此聊天";
    $view_media_notice = "查看媒体";
    $you = "你";
    $code_or_password_wrong = "您的密码或代码不正确。";
    $adminnotice = "行政人员";
}

?>