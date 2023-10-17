<?php
    function getTranslatedContent($from){
        $it = array(
            "home_title" => "",
            "home_description" => "Denvelope ti permette di salvare, modificare e condividere il tuo codice in modo semplice, veloce e affidabile",
            "home_hero_title" => "Da 'Hello, World!',<br>ai tuoi grandi progetti",
            "home_hero_subtitle" => "Denvelope ti permette di salvare, modificare e condividere il tuo codice in modo semplice, veloce e affidabile",
            "home_beta_message" => "Una versione BETA è in arrivo!",
            "home_signup_button_mob" => "Registrati",
            "home_feature_store_title" => "Non perdere più il tuo codice",
            "home_feature_store_description" => "Con Denvelope puoi ora salvare il tuo codice in un posto sicuro. Non dovrai più preoccuparti di perdere i tuoi progetti, non importa quanto piccoli o grandi sono.",
            "home_feature_view_edit_title" => "Visualizza e Modifica il tuo codice dovunque tu sia",
            "home_feature_view_edit_description" => "Denvelope ti permette di vedere e modificare il tuo codice da qualunque dispositivo con l'editor integrato (con l'evidenziazione della sintassi e l'auto-completamento).",
            "home_feature_share_title" => "Hai bisogno di condividere un pezzo di codice o un intero progetto?",
            "home_feature_share_description" => "Con Denvelope puoi semplicemente cliccare un bottone e il link a quel file o progetto è copiato e pronto per essere condiviso.",
            "header_signup" => "Registrati",
            "header_login" => "Accedi",
            "header_logout" => "Esci",
            "header_settings" => "Impostazioni",
            "account_title" => "Il tuo account",
            "account_add_source_code" => "Aggiungi sorgente",
            "account_choose_type" => "Scegli tipo",
            "account_single_file" => "File Singolo",
            "account_single_file_title" => "Aggiungi sorgente",
            "account_single_file_select_language" => "Scegli linguaggio",
            "account_file_language_text_file" => "File di Testo",
            "account_file_language_other" => "Altro",
            "account_file_language_other_archive" => "Altro (Archivio)",
            "account_file_language_other_audio" => "Altro (Audio)",
            "account_file_language_other_document" => "Altro (Documento)",
            "account_file_language_other_image" => "Altro (Immagine)",
            "account_file_language_other_video" => "Altro (Video)",
            "account_single_file_name" => "Nome",
            "account_single_file_add_file" => "Aggiungi File",
            "account_single_file_file_added" => "File Aggiunto",
            "account_save" => "Salva",
            "account_back" => "Indietro",
            "account_project" => "Progetto",
            "account_project_title" => "Aggiungi cartella progetto",
            "account_project_name" => "Nome",
            "account_project_add_folder" => "Aggiungi Cartella",
            "account_project_folder_added" => "Cartella Aggiunta",
            "account_multiple_files" => "Più File",
            "account_multiple_files_title" => "Aggiungi più file",
            "account_multiple_files_add_files" => "Aggiungi File",
            "account_multiple_files_files_added" => "File Aggiunti",
            "account_multiple_files_file_added" => "File Aggiunto",
            "account_multiple_files_error_choose_some_files" => "Scegli i file da caricare",
            "account_cancel" => "Annulla",
            "account_create_new_file" => "Crea un nuovo file",
            "account_create_new_file_title" => "Crea un nuovo file",
            "account_create_new_file_select_language" => "Scegli linguaggio",
            "account_create_new_file_name" => "Nome",
            "account_create_new_folder" => "Crea una nuova cartella",
            "account_create_new_folder_title" => "Crea una nuova cartella",
            "account_create_new_folder_name" => "Nome",
            "account_upgrade_plan" => "Effettua Upgrade",
            "account_used_storage_space" => "Spazio Utilizzato",
            "account_my_source_codes" => "I Miei Sorgenti",
            "account_sort_by" => "Ordina Per",
            "account_sort_by_name" => "Nome",
            "account_sort_by_date" => "Data",
            "account_sort_by_size" => "Dimensioni",
            "account_sort_by_language" => "Linguaggio",
            "account_sort_by_last_modified" => "Ultima modifica",
            "account_context_menu_view" => "Vedi",
            "account_context_menu_share" => "Condividi",
            "account_context_menu_delete" => "Elimina",
            "account_context_menu_info" => "Info",
            "account_context_menu_download" => "Scarica",
            "account_context_menu_rename" => "Rinomina",
            "account_context_menu_add_source" => "Aggiungi Sorgente",
            "account_context_menu_new_file" => "Nuovo File",
            "account_context_menu_new_folder" => "Nuova Cartella",
            "account_empty_folder" => "Questa cartella è vuota",
            "account_search_box" => "Cerca",
            "account_search_by_name" => "Nome",
            "account_search_by_date" => "Data",
            "account_search_by_size" => "Dimensioni",
            "account_search_by_language" => "Linguaggio",
            "account_search_by_last_modified" => "Ultima modifica",
            "account_no_elements_found_on_search" => "Non ci sono elementi come questo",
            "account_folder_info_name" => "Nome",
            "account_folder_info_created" => "Creata",
            "account_folder_info_last_modified" => "Ultima modifica",
            "account_folder_info_size" => "Dimensioni",
            "account_folder_info_folders" => "Cartelle",
            "account_folder_info_files" => "File",
            "account_folder_info_close" => "Chiudi",
            "account_folder_buttons_view" => "Vedi",
            "account_folder_buttons_share" => "Condividi",
            "account_folder_buttons_delete" => "Elimina",
            "account_folder_buttons_info" => "Info",
            "account_folder_buttons_download" => "Scarica",
            "account_source_code_info_name" => "Nome",
            "account_source_code_info_created" => "Creato",
            "account_source_code_info_last_modified" => "Ultima modifica",
            "account_source_code_info_language" => "Linguaggio",
            "account_source_code_info_type" => "Tipo",
            "account_source_code_info_size" => "Dimensioni",
            "account_source_code_info_close" => "Chiudi",
            "account_source_code_buttons_view" => "Vedi",
            "account_source_code_buttons_share" => "Condividi",
            "account_source_code_buttons_delete" => "Elimina",
            "account_source_code_buttons_info" => "Info",
            "account_source_code_buttons_download" => "Scarica",
            "account_message_link_copied_to_clipboard" => "Link copiato negli appunti",
            "account_message_file_deleted" => "File eliminato con successo",
            "account_message_error_deleting_file" => "C'è stato un errore nel cancellare il file",
            "account_message_folder_deleted" => "Cartella eliminata con successo",
            "account_message_error_deleting_folder" => "C'è stato un errore nel cancellare la cartella",
            "account_message_file_renamed" => "File rinominato con successo",
            "account_message_error_renaming_file" => "C'è stato un errore nel rinominare il file",
            "account_message_folder_renamed" => "Cartella rinominata con successo",
            "account_message_error_renaming_folder" => "C'è stato un errore nel rinominare la cartella",
            "account_message_ajax_file_upload_waiting_for_response" => "Aspettando una risposta",
            "account_view_source_message_file_saved" => "File salvato con successo",
            "account_view_source_message_error_file_save" => "C'è stato un errore nel salvare il file",
            "account_view_source_message_ignore" => "Questo file è stato contrassegnato per essere ignorato dall'autore",
            "account_view_source_close" => "Chiudi",
            "account_view_source_info" => "Info",
            "account_view_source_share" => "Condividi",
            "account_view_source_download" => "Scarica",
            "account_view_source_edit" => "Modifica",
            "account_view_source_stop_editing" => "Finisci Modifica",
            "account_view_source_save" => "Salva",
            "account_view_source_delete" => "Elimina",
            "account_view_source_info_name" => "Nome",
            "account_view_source_info_created" => "Creato",
            "account_view_source_info_last_modified" => "Ultima modifica",
            "account_view_source_info_language" => "Linguaggio",
            "account_view_source_info_type" => "Tipo",
            "account_view_source_info_size" => "Dimensioni",
            "account_view_source_info_close" => "Chiudi",
            "account_view_source_source_not_available" => "Sorgente non disponibile",
            "account_view_source_save_to_my_account" => "Salva nel mio account",
            "account_rename_file" => "Rinomina File",
            "account_rename_file_name" => "Nome",
            "account_rename_folder" => "Rinomina Cartella",
            "account_rename_folder_name" => "Nome",
            "settings_title" => "Impostazioni Account",
            "settings_updated_correctly" => "Impostazioni modificate correttamente",
            "settings_update_error" => "C'è stato un errore nel salvare le impostazioni",
            "settings_side_menu_title" => "Impostazioni",
            "settings_general" => "Generali",
            "settings_plan" => "Piano",
            "settings_security" => "Sicurezza",
            "settings_advanced" => "Avanzate",
            "settings_info" => "Info",
            "settings_privacy" => "Privacy",
            "settings_support" => "Supporto",
            "settings_general_username" => "Nome Utente",
            "settings_general_confirm" => "Conferma",
            "settings_general_email" => "Email",
            "settings_general_language" => "Lingua",
            "settings_general_theme" => "Tema",
            "settings_general_email_preferences" => "Preferenze Email",
            "settings_general_email_preferences_on_new_logins" => "Nuovi Log In",
            "settings_general_email_preferences_save" => "Salva Preferenze",
            "settings_plan_title" => "Piano",
            "settings_plan_next_renewal" => "Prossimo Rinnovo",
            "settings_plan_subscritption_canceled" => "Sottoscrizione Cancellata",
            "settings_plan_subscritption_canceled_active_until" => "Attiva fino a",
            "settings_plan_subscritption_downgraded" => "Sottoscrizione Cambiata",
            "settings_plan_subscritption_downgraded_new_plan" => "Il tuo nuovo piano sarà",
            "settings_plan_subscritption_downgraded_new_plan_from" => "dal",
            "settings_plan_current" => "Attuale",
            "settings_plan_tier_free" => "Gratuito",
            /*
            "settings_plan_tier_basic" => "Basic",
            "settings_plan_tier_basic_plusplus" => "Basic++",
            "settings_plan_tier_standard" => "Standard",
            "settings_plan_tier_standard_plusplus" => "Standard++",
            "settings_plan_tier_advanced" => "Avanzato",
            */
            "settings_plan_tier_personal" => "Personal",
            "settings_plan_tier_personal_plus" => "Personal Plus",
            "settings_plan_tier_professional" => "Professional",
            "settings_plan_tier_professional_plus" => "Professional Plus",
            "settings_plan_tier_enterprise" => "Enterprise",
            "settings_plan_tier_custom" => "Personalizzato",
            "settings_plan_need_more" => "Vuoi di più? Contattaci",
            "settings_plan_month" => "mese",
            "settings_plan_storage" => "di Spazio",
            "settings_plan_credit_or_debit_card" => "Carta di credito o debito",
            "settings_plan_upgrade_plan" => "Cambia Piano",
            "settings_plan_change_plan" => "Cambia Piano",
            "settings_plan_cancel_plan" => "Cancella Piano",
            "settings_plan_downgrade_disclaimer" => "<span>Nota: </span>se scegli di effettuare il downgrade del tuo piano o di cancellarlo e lo spazio di archiviazione del nuovo piano non è a sufficienza per mantenere tutti i tuoi file il tuo account verrà svuotato. Quindi fai attenzione a scaricare tutti i file che ti interessano prima di effettuare il downgrade o la cancellazione.<br>Ci scusiamo per il disagio.",
            "settings_plan_cancel_confirm_message" => "Clicca un'altra volta se decidi di continuare",
            "settings_plan_card" => "Carta",
            "settings_plan_next_billing_cycle" => "Prossimo ciclo di fatturazione",
            "settings_plan_change_card" => "Cambia Carta",
            "settings_plan_faq" => "FAQ",
            "settings_plan_faq_storage_space_only_difference" => "È lo spazio di archiviazione l'unica differenza tra tutti i piani?",
            "settings_plan_faq_storage_space_only_difference_answer" => "Sì, lo è. A noi non piace bloccarti l'accesso a funzionalità che tu potresti volere solo perchè non necessiti di molto spazio di archiviazione oppure perchè non puoi permetterti il piano con quella funzionalità in particolare",
            "settings_security_change_password" => "Cambia Password",
            "settings_security_current_password" => "Password Attuale",
            "settings_security_new_password" => "Nuova Password",
            "settings_security_two_factor_authentication" => "Autenticazione A Due Fattori",
            "settings_security_two_factor_authentication_phone_number" => "Numero di Telefono",
            "settings_security_two_factor_authentication_enable" => "Attiva",
            "settings_security_two_factor_authentication_enabled_message" => "Hai abilitato l'Autenticazione A Due Fattori",
            "settings_security_two_factor_authentication_remove_button" => "Rimuovi",
            "settings_security_two_factor_authentication_authy_app_needed_message" => "L'app <img src=\"../../img/icons/authy.svg\" style=\"height: 15px; vertical-align: bottom;\" alt=\"\">&thinsp;<span style=\"color: white;\">Authy</span> è necessaria per far funzionare questo",
            "settings_security_two_factor_authentication_also_send_sms" => "Inviami anche un SMS",
            "settings_security_logout_from_all_devices" => "Esci Da Tutti I Dispositivi",
            "settings_security_logout" => "Esci",
            "settings_security_active_devices" => "Dispositivi Attivi",
            "settings_security_last_activity" => "Ultima Attività",
            "settings_security_location" => "Posizione",
            "settings_security_this_device" => "Questo Dispositivo",
            "settings_advanced_delete_account" => "Elimina Account",
            "settings_advanced_delete" => "Elimina",
            "settings_advanced_before_delete_message" => "Clicca un'altra volta per confermare questa azione.<br>Ci dispiace vederti andare via, ma questo significa che c'è ancora spazio per migliorare e puoi starne certo che miglioreremo",
            "settings_info_account_info" => "Informazioni Sull'Account",
            "settings_info_account_created" => "Account creato",
            "settings_info_files_uploaded_since" => "File caricati dal",
            "settings_info_files_currently_uploaded" => "File attualmente caricati",
            "settings_info_average_file_size" => "Dimensione media file",
            "settings_info_smallest_file_currently_uploaded" => "File più piccolo attualmente caricato",
            "settings_info_biggest_file_currently_uploaded" => "File più grande attualmente caricato",
            "settings_info_files_uploaded_this_week" => "File caricati questa settimana",
            "settings_info_files_edited_after_uploading_since" => "File modificati dopo il caricamento dal",
            "settings_info_used_storage" => "Spazio utilizzato",
            "settings_info_terms_of_service" => "Termini di Servizio",
            "settings_info_privacy_policy" => "Politica sulla Privacy",
            "settings_info_cookie_policy" => "Politica sui Cookie",
            "settings_info_contact_us" => "Contattaci",
            "settings_info_app_ver" => "Versione",
            "settings_info_latest_changelog" => "Ultimo Changelog",
            "settings_privacy_logs" => "Log",
            "settings_privacy_logs_login" => "ACCESSI",
            "settings_privacy_logs_logout" => "DISCONNESSIONI",
            "settings_privacy_logs_username_changes" => "CAMBI NOME UTENTE",
            "settings_privacy_logs_email_changes" => "CAMBI EMAIL",
            "settings_privacy_logs_password_changes" => "CAMBI PASSWORD",
            "settings_privacy_logs_files_uploaded" => "FILE CARICATI",
            "settings_privacy_logs_folders_uploaded" => "CARTELLE CARICATE",
            "settings_privacy_logs_files_created" => "FILE CREATI",
            "settings_privacy_logs_folders_created" => "CARTELLE CREATE",
            "settings_privacy_logs_files_deleted" => "FILE ELIMINATI",
            "settings_privacy_logs_folders_deleted" => "CARTELLE ELIMINATE",
            "settings_privacy_opt_out_logs" => "Disabilita raccolta di log",
            "settings_privacy_delete_logs" => "Elimina Log",
            "settings_privacy_disclaimer" => "<span>Nota: </span>cancellare questi log risulterà nella perdita delle attività del tuo account e per questo motivo noi non potremmo riuscire a risolvere problemi avvenuti prima dell'eliminazione di questi<br>Usiamo questi log solo per motivi di sicurezza e per meglio capire problemi che potrebbero capitare, se desideri aiutarci non cancellare i log, ma la decisione spetta a te<br>Questo non ci proibirà di raccogliere log immediatamente dopo la loro eliminazione, puoi disabilitare la loro raccolta permanentemente spuntando la casella sopra il bottone",
            "settings_support_my_cases" => "I miei casi",
            "contact_us_title" => "Contattaci",
            "contact_us_subject" => "Oggetto",
            "contact_us_message" => "Messaggio",
            "contact_us_submit" => "Invia",
            "signup_title" => "Registrati",
            "signup_or" => "o",
            "signup_or_login" => "Accedi",
            "signup_username" => "Nome Utente",
            "signup_email" => "Email",
            "signup_password" => "Password",
            "signup_g_recaptcha_text" => "Questo sito è protetto da reCAPTCHA e soggetto alla <a href=\"https://policies.google.com/privacy\">Privacy Policy</a> e ai <a href=\"https://policies.google.com/terms\">Termini di Servizio</a> di Google.",
            "signup_terms_privacy_statement" => "Registrandoti accetti i nostri <a href=\"../terms\"><i class=\"fas fa-balance-scale\"></i> Termini di Servizio</a> e la nostra <a href=\"../privacy\"><i class=\"fas fa-user-shield\"></i> Privacy Policy</a>",
            "signup_signup_button" => "Registrati",
            "login_title" => "Accedi",
            "login_please_login_to_continue" => "Accedi per continuare",
            "login_or" => "o",
            "login_or_signup" => "Registrati",
            "login_username_email" => "Nome Utente / Email",
            "login_password" => "Password",
            "login_2fa_code" => "Codice 2FA",
            "login_send_2fa_code_via_sms" => "Invia il Codice 2FA Code tramite SMS",
            "login_resend_2fa_code_via_sms" => "Reinvia il Codice 2FA",
            "login_remember_me" => "Ricordami",
            "login_login_button" => "Accedi",
            "login_forgot_password" => "Password dimenticata?",
            "forgot_password_title" => "Password Dimenticata",
            "forgot_password_form_title" => "Recupera Password",
            "forgot_password_back_to" => "torna ad",
            "forgot_password_back_to_login" => "Accedi",
            "forgot_password_email" => "Email",
            "forgot_password_submit" => "Invia",
            "reset_password_title" => "Reimposta la tua password",
            "reset_password_new_password" => "Nuova Password",
            "reset_password_repeat_password" => "Ripeti Password",
            "reset_password_submit" => "Invia",
            "signup_error_username_empty" => "Inserisci un nome utente",
            "signup_error_username_too_short" => "Il nome utente deve essere di almeno 4 caratteri",
            "signup_error_username_too_long" => "Il nome utente può avere al massimo 15 caratteri",
            "signup_error_username_invalid" => "Inserisci un nome utente valido<br>Usa solo caratteri alfanumerici, punti, trattini bassi e trattini",
            "signup_error_username_already_taken" => "Nome utente già preso",
            "signup_error_email_empty" => "Inserisci un'email",
            "signup_error_email_too_long" => "L'email non può avere più di 255 caratteri",
            "signup_error_email_invalid" => "Inserisci un'email valida",
            "signup_error_email_already_taken" => "Esiste già un utente con questa email",
            "signup_error_password_empty" => "Inserisci una password",
            "signup_error_password_too_short" => "La password deve avere almeno 8 caratteri",
            "login_error_username_email_empty" => "Inserisci il tuo nome utente o l'email",
            "login_error_username_email_invalid" => "Nome utente o email non validi",
            "login_error_username_email_does_not_exist" => "Nome utente o email non esistono",
            "login_error_password_empty" => "Inserisci la tua password",
            "login_error_password_invalid" => "Password non valida",
            "login_error_password_not_correct" => "Password non corretta",
            "login_error_2fa_code_empty" => "Inserisci il tuo codice 2FA",
            "login_error_2fa_code_too_short" => "Il codice 2FA deve essere lungo 7 caratteri",
            "login_error_2fa_code_too_long" => "Il codice 2FA deve essere lungo 7 caratteri",
            "login_error_2fa_code_invalid" => "Il codice 2FA deve contenere solo numeri",
            "login_error_2fa_code_wrong" => "Il codice 2FA non è corretto",
            "forgot_password_error_empty_field" => "Inserisci la tua email",
            "forgot_password_error_email_too_long" => "L'email non può avere più di 255 caratteri",
            "forgot_password_error_invalid_email" => "Inserisci un'email valida",
            "forgot_password_error_email_does_not_exist" => "Non c'è nessun utente registrato con questa email",
            "forgot_password_error_expired_tokens" => "La tua richiesta di reimpostazione della password è scaduta.<br>Invia una nuova richiesta",
            "forgot_password_error_invalid_tokens" => "La tua richiesta di reimpostazione della password non è valida.<br>Invia una nuova richiesta",
            "forgot_password_error_user_do_not_exist" => "Non c'è nessun utente registrato con questa email",
            "reset_password_error_empty_new_password" => "Inserisci una password",
            "reset_password_error_passwords_do_not_match" => "Le password non sono uguali",
            "reset_password_error_password_too_short" => "La password deve avere almeno 8 caratteri",
            "reset_password_error_empty_repeat_password" => "Inserisci una password",
            "contact_error_subject_empty" => "Inserisci un'oggetto",
            "contact_error_subject_too_long" => "L'oggetto è troppo lungo, mantienilo sotto i 100 caratteri",
            "contact_error_message_empty" => "Inserisci un messaggio",
            "contact_error_message_too_long" => "Il messaggio è troppo lungo, mantienilo sotto i 5000 caratteri",
            "signup_message_box_success" => "Congratulazioni! <br><br> Il tuo account è stato creato con successo. <br><br> Ora devi solo confermarlo dall'email che ti abbiamo inviato, e sei a posto",
            "login_message_box_not_activated" => "Il tuo account non è stato ancora attivato <br><br> Controlla la tua email, anche la cartella spam e clicca sul link per confermare il tuo account <br><br> Nel caso non la trovi clicca reinvia e ne riceverai un'altra <br><br> <button type=\"submit\" name=\"resend-confirm-email-button\">Reinvia email</button>",
            "login_message_box_activation_email_resent" => "Una nuova email è stata inviata <br><br> Ora devi solo confermare l'account dall'email che ti abbiamo inviato, e sei a posto",
            "forgot_password_message_box_email_sent" => "Un'email contenente il link per reimpostare la tua password è stata inviata",
            "confirm_account_title" => "Conferma Account",
            "confirm_account_namespace_congratulations" => "congratulazioni",
            "confirm_account_username" => "username",
            "confirm_account_congratulations_username" => "Congratulazioni {username}! Il tuo account è stato attivato!",
            "confirm_account_redirect_to_login" => "In 10 secondi sarai reindirizzato alla pagina di accesso!",
            "cookie_banner_description" => "Usiamo i cookie per rendere possibile il corretto funzionamento di Denvelope per te. Utilizzando il nostro sito, accetti le nostre modalità di utilizzo dei cookie.",
            "cookie_banner_learn_more" => "Ulteriori informazioni",
            "terms_of_service_title" => "Termini di Servizio",
            "privacy_policy_title" => "Politica sulla Privacy",
            "cookie_policy_title" => "Politica sui Cookie",
            "cookie_policy_cookies_section_title" => "Cookie",
            "cookie_policy_cookies_section_text" => "I cookie sono piccoli file di dati inviati al tuo browser quando visiti un sito. Utilizziamo sia i nostri cookie che quelli di terze parti per varie finalità:",
            "cookie_policy_cookies_section_list_login" => "Farti accedere ai nostri servizi",
            "cookie_policy_cookies_section_list_remember_settings" => "Ricordare preferenze e impostazioni",
            "cookie_policy_cookies_section_list_keep_account_secure" => "Mantenere al sicuro il tuo account",
            "cookie_policy_cookies_section_list_understand_use" => "Capire meglio in che modo gli utenti utilizzano i nostri servizi e migliorarli",
            "cookie_policy_opt_out_section_title" => "Disattivazione",
            "cookie_policy_opt_out_section_text" => "Puoi impostare il tuo browser in modo che non accetti i cookie, ma ciò potrebbe limitare la possibilità di utilizzare i nostri Servizi.",
            "email_password_changed_subject" => "La tua password è stata reimpostata",
            "email_password_changed_password_correctly_reset" => "La tua password per Denvelope è stata reimpostata correttamente",
            "email_password_changed_did_not_perform_action_enter_email" => "Se non hai eseguito questa azione, puoi recuperare l'accesso inserendo la tua email",
            "email_password_changed_did_not_perform_action_enter_email_into_form" => "nel modulo all'indirizzo",
            "email_password_changed_do_not_share_your_password" => "Ricordati di non condividere la tua password con nessuno. Noi non la chiederemo mai.",
            "email_account_confirmed_subject" => "Account confermato!",
            "email_account_confirmed_congratulations" => "Congratulazioni,",
            "email_account_confirmed_account_confirmed" => "il tuo account è stato confermato!",
            "email_account_confirmed_enjoy_what_we_offer" => "Noi speriamo tu apprezzi cosa abbiamo da offrire,",
            "email_account_confirmed_in_case_need_help" => "in caso tu abbia bisogno potrai contattarci a",
            "email_account_confirmed_help_through_contact_form" => "oppure attraverso il modulo di contatto all'indirizzo",
            "email_support_request_received_subject" => "Richiesta di Supporto Ricevuta",
            "email_support_request_received_subject_case" => "CASO",
            "email_support_request_received_thank_you_for_contacting" => "Grazie per aver contattato Denvelope",
            "email_support_request_received_new_support_case_opened" => "Abbiamo aperto un nuovo caso di supporto per rispondere alla tua richiesta",
            "email_support_request_received_case_details" => "I dettagli del tuo caso",
            "email_support_request_received_case_id" => "ID Caso",
            "email_support_request_reply_sender" => "Mittente",
            "email_support_request_received_note_answer_time" => "Nota: ci vogliono di solito 24 ore per ricevere una risposta",
            "email_new_login_subject" => "Nuovo accesso rilevato",
            "email_new_login_login_notice" => "Abbiamo notato un nuovo accesso",
            "email_new_login_informations" => "Informazioni",
            "email_new_login_platform" => "Piattaforma",
            "email_new_login_browser" => "Browser",
            "email_new_login_ip_address" => "Indirizzo IP",
            "email_new_login_time" => "Orario",
            "email_new_login_location" => "Posizione",
            "email_new_login_note_information_accuracy" => "Nota: queste informazioni potrebbero non essere del tutto accurate",
            "email_new_login_invalidate_session" => "In caso tu non riconosca questa attività clicca l'indirizzo qui sotto e la sessione sarà invalidata",
            "email_new_login_security_concerns_change_password" => "Se sei preoccupato della sicurezza del tuo account considera il cambiare la password",
            "email_new_login_no_cookie_no_remember_me_feature" => "Questo accesso proviene da una sessione senza la funzione 'Ricordami'",
            "email_new_login_no_cookie_logout_not_possible_right_now" => "Attualmente non è possibile eseguire l'uscita da queste sessioni, ma ci stiamo lavorando",
            "email_new_login_no_cookie_remember_not_to_share_password_while_working_on_feature" => "Quindi mentre noi stiamo lavorando su questa funzionalità, ricordati di non condividere la tua password con nessuno. Noi non la chiederemo mai.",
            "email_email_changed_subject" => "Richiesta cambio email",
            "email_email_changed_old_email_request_to" => "La tua email di Denvelope è stata richiesta di cambiare a",
            "email_email_changed_old_did_not_perform_action" => "Se non hai eseguito questa azione, contatta immediatamente il supporto a",
            "email_email_changed_old_did_not_perform_action_contact_form_website" => "O se preferisci usare il modulo di contatto sul nostro sito all'indirizzo",
            "email_email_changed_old_remember_not_share_password" => "Ricordati di non condividere la tua password con nessuno. Noi non la chiederemo mai.",
            "email_email_changed_new_email_updated_correctly" => "La tua email di Denvelope è stata aggiornata correttamente a questa, da",
            "email_email_changed_new_did_not_perform_action" => "Se non hai eseguito questa azione, contatta il supporto a",
            "email_email_changed_new_did_not_perform_action_contact_form_website" => "O se preferisci usare il modulo di contatto sul nostro sito all'indirizzo",
            "email_email_changed_new_did_not_perform_action_subject" => "Con oggetto 'Cambio Email Errato'",
            "email_email_changed_new_alternative_contact" => "Oppure in alternativa contatta",
            "email_email_changed_new_alternative_contact_wrong_email_typed" => "dicendo che è stata inserita l'email sbagliata",
            "email_email_changed_new_contact_support_for_reset" => "E di contattare il supporto per reimpostarla alla precedente, o a quella nuova corretta.",
            "email_signup_subject" => "Conferma il tuo account Denvelope",
            "email_signup_welcome" => "Benvenuto in Denvelope!",
            "email_signup_excited_to_have_you" => "Siamo felici di averti qui.",
            "email_signup_last_step" => "Ma c'è ancora un ultimo passo prima che tu possa vedere cosa offriamo e speriamo tu lo apprezzerai.",
            "email_signup_click_this_link" => "Clicca questo indirizzo e avrai finito",
            "email_signup_have_question_shoot_us_email" => "Se hai una qualsiasi domanda sentiti libero di inviarci un'email a",
            "email_signup_contact_us_website_form" => "O se preferisci usare il modulo di contatto sul nostro sito all'indirizzo",
            "email_signup_received_by_mistake" => "Se pensi di aver ricevuto questa email per errore puoi pure ignorala",
            "email_support_request_reply_subject_reply_to" => "Risposta A",
            "email_support_request_reply_case_id" => "ID Caso",
            "email_support_request_reply_case_closed" => "CHIUSO",
            "email_support_request_reply_status_closed" => "Stato: Chiuso",
            "email_password_reset_subject" => "Reimposta la tua password per Denvelope",
            "email_password_reset_reset_your_password" => "Reimposta la tua password",
            "email_password_reset_password_reset_request_received" => "Abbiamo ricevuto una richiesta di reimpostazione della password per il tuo account",
            "email_password_reset_click_link_to_reset" => "Clicca questo indirizzo per reimpostare la tua password",
            "email_password_reset_link_expires_in" => "Questo indirizzo scadrà tra 10 minuti",
            "email_password_reset_have_question_shoot_us_email" => "Se hai una qualsiasi domanda sentiti libero di inviarci un'email a",
            "email_password_reset_contact_us_website_form" => "O se preferisci usare il modulo di contatto sul nostro sito all'indirizzo",
            "email_password_reset_did_not_request_password_will_not_change" => "Se non hai richiesto una reimpostazione della password ignora pure questa email e la password non cambierà",
            "email_ended_subscription_subject" => "La tua sottoscrizione a Denvelope è terminata",
            "email_ended_subscription_sorry_see_you_go" => "Ci dispiacce vederti andare",
            "email_ended_subscription_your_subscription_to_the" => "La tua sottoscrizione al piano",
            "email_ended_subscription_your_subscription_to_the_plan_has_ended" => "è terminata,",
            "email_ended_subscription_account_downgraded_to_free_tier" => "quindi il tuo account è stato portato al piano Gratuito",
            "email_ended_subscription_total_size_greater_free_tier" => "Sfortunatamente, la dimensione totale dei tuoi file era più grande di quella offerta dal piano Gratuito,",
            "email_ended_subscription_account_emptied_as_stated_terms_of_service" => "così il tuo account è stato svuotato, come scritto nei nostri Termini di Servizio all'indirizzo",
            "email_ended_subscription_account_emptied_message_before_confirm" => "e nel messaggio apparso prima di confermare la cancellazione o il downgrade del piano",
            "email_ended_subscription_always_working_to_make_users_happy" => "Noi lavoriamo sempre per far felici i nostri utenti,",
            "email_ended_subscription_if_have_time_consider_leaving_feedback" => "se hai il tempo, considera di lasciare un feedback sulla tua scelta di cancellare la tua sottoscrizione a",
            "email_subscription_payment_failed_subject" => "Il pagamento per la tua sottoscrizione a Denvelope è fallito",
            "email_subscription_payment_failed_payment_for" => "Il pagamento per il tuo piano",
            "email_subscription_payment_failed_payment_for_plan_failed" => "è fallito",
            "email_subscription_payment_failed_we_cancelled_your_subscription" => "Abbiamo così cancellato la tua sottoscrizione a Denvelope,",
            "email_subscription_updated_subject" => "La tua sottoscrizione a Denvelope è stata modificata",
            "email_subscription_updated_total_size_greater_new" => "Sfortunatamente, la dimensione totale dei tuoi file era più grande di quella offerta dal piano",
            "email_subscription_updated_total_size_greater_new_tier" => "",
            "email_subscription_updated_from" => "La tua sottoscrizione a Denvelope è stata modificata con successo dal piano",
            "email_subscription_updated_from_free_to" => "Il tuo account Denvelope è stato aumentato con successo dal piano Gratuito al piano",
            "email_subscription_updated_from_to" => "al piano",
            "email_subscription_updated_from_to_tier" => "",
            "email_subscription_updated_from_free_to_tier" => "",
            "email_all_the_best" => "Cordiali saluti,",
            "email_the_denvelope_team" => "Il Team di Denvelope",
            "footer_pricing" => "Prezzi",
            "footer_contact_us" => "Contattaci",
            "footer_cookies" => "Coookie",
            "footer_privacy" => "Privacy",
            "footer_terms" => "Termini",
            "support_center_title" => "Centro di Supporto",
            "support_center_no_open_support_cases" => "Non ci sono casi di supporto aperti",
            "support_center_message" => "Messaggio",
            "support_center_mark_as_closed" => "Segna come chiuso",
            "support_center_error_reply_message_too_long" => "Messaggio di risposta troppo lungo",
            "support_center_error_reply_message_empty" => "Il messaggio di risposta non può essere vuoto",
            "pricing_title" => "Prezzi",
            "pricing_note" => "Nota: attualmente quando registri un account non è possibile scegliere immediatamente il piano con cui cominciare, comincierai con il piano Gratuito e se vorrai / necessiterai un upgrade basterà semplicemente cliccare il bottone \"Effettua Upgrade\", oppure andare nelle impostazioni e da lì effettuare l'upgrade al piano più adatto a te",
            "pricing_start_now_for_free" => "Comincia ora GRATUITAMENTE",
            "admin_panel_title" => "Pannello di Amministrazione",
            "admin_panel_query_execute" => "Esegui",
            "errors_button_signup" => "Registrati",
            "errors_button_login" => "Accedi",
        );

        return $it[$from];
    }
?>