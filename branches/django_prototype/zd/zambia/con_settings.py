import datetime
import dateutil.parser

from zambia.models import Config

con_config = {}

def get_all_con_configs():
    if len(con_config) == 0:
        for i in Config.objects.all():
            if i.configtype == 'date':
                con_config[i.configname] = dateutil.parser.parse(i.configvalue)
            elif i.configtype == 'number':
                con_config[i.configname] = int(i.configvalue)
            elif i.configtype == 'bool':
                con_config[i.configname] = i.configvalue.upper() == 'TRUE'
            else:
                con_config[i.configname] = i.configvalue
# Have some defaults if there's nothing in the database
        if len(con_config) == 0:
            for i in (
                ('string', 'CON_NAME', 'Time_t Con 2038', 'The name of the convention.'),
                ('string', 'CON_LOGO_IMAGE', 'images/logo.gif', 'Image to show at the top of pages.'),
                ('string', 'ADMIN_EMAIL', 'zambia@example.com', 'Where users should send mail about problems with Zambia itself.'),
                ('string', 'BRAINSTORM_EMAIL', 'brainstorm@example.com', 'Where users should send email about the brainstorming suggestions.'),
                ('string', 'PROGRAM_EMAIL', 'program@example.com', 'Where users should send email about programming items.'),
                ('string', 'REG_EMAIL', 'registration@example.com', 'Where users should send email about registration issues.'),
                ('string', 'FIRST_DAY_START_TIME', '17:30', 'When programming starts on the first day.'),
                ('string', 'OTHER_DAY_START_TIME', '8:30', "When programming starts on days that aren't the first."),
                ('string', 'OTHER_DAY_STOP_TIME', '25:00', "When programming stops on days that aren't the last."),
                ('string', 'LAST_DAY_STOP_TIME', '16:00', 'When programming stop on the last day.'),
                ('string', 'STANDARD_BLOCK_LENGTH', '1:30', 'Normally schedule in blocks of this size.'),
                ('string', 'DEFAULT_DURATION', '1:15', 'Must match the format selected by DURATION_IN_MINUTES.'),
                ('string', 'SMTP_ADDRESS', 'smtp-out.example.com', 'Host to connect to for sending outgoing email.'),
                ('string', 'SWIFT_DIRECTORY', '/home/zambia_admin/Swift/', 'Location of the installed Swift library.'),
                ('string', 'SECOND_LANG', 'FRENCH', 'Name of the second language if BILINGUAL is True.'),
                ('string', 'SECOND_TITLE_CAPTION', 'Titre en fran&ccedil;ais', 'Title caption in the second language.'),
                ('string', 'SECOND_DESCRIPTION_CAPTION', 'Description en fran&ccedil;ais', 'Description caption in the second language.'),
                ('string', 'SECOND_BIOGRAPHY_CAPTION', 'Biographie en fran&ccedil;ais', 'Biography caption in the second language.'),
                ('string', 'BASESESSIONDIR', '/var/lib/php5', 'Directory on the web server where session information is stored.'),

                ('date', 'CON_START_DATIM', '2038-03-06 00:00:00', 'Date when the convention starts.'),

                ('number', 'CON_NUM_DAYS', '5', 'How many days to schedule con activities for.  The code works for values of 1 through 8.'),
                ('number', 'DAY_CUTOFF_HOUR', '8', 'Times before this hour (of 0-23) are considered part of the previous day.'),
                ('number', 'PREF_TTL_SESNS_LMT', '10', 'Input data verification limit for preferred total number of sessions.'),
                ('number', 'PREF_DLY_SESNS_LMT', '5', 'Input data verification limit for preferred daily limit of sessions.'),
                ('number', 'AVAILABILITY_ROWS', '8', 'Number of rows of availability records to render.'),
                ('number', 'MAX_BIO_LEN', '1000', 'Maximum length (in characters) permitted for participant biographies.'),

                ('bool', 'DURATION_IN_MINUTES', 'False', 'When TRUE, durations are displayed as mmm, when FALSE, as hh:mm.  This affects session edit/create page only, not reports.'),
                ('bool', 'MY_AVAIL_KIDS', 'False', 'Enables questions regarding no. of kids in Fasttrack on "My Availability".'),
                ('bool', 'ENABLE_SHARE_EMAIL_QUESTION', 'True', 'Enables question regarding sharing participant email address.'),
                ('bool', 'ENABLE_BESTWAY_QUESTION', 'False', 'Enables question regarding best way to contact participant.'),
                ('bool', 'BILINGUAL', 'True', 'Triggers extra fields in Session and "My General Interests".'),
                ('bool', 'SHOW_BRAINSTORM_LOGIN_HINT', 'False', 'If TRUE, the hint on how to log in to brainstorming is shown on the login screen.'),
                ('bool', 'ANALYTICS_ENABLED', 'False', 'If True, Google Analytics javascript is included in the footers of pages.  See Ben for details.'),
            ):
                c = Config(configtype=i[0], configname = i[1], configvalue = i[2], configdescription = i[3])
                c.save()
            return get_all_con_configs()

    return con_config    

def get_con_config(name):
    if len(con_config) == 0:
        get_all_con_configs()
    return con_config.get(name)
