# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models
import zambia.models

class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0013_auto_20160525_1443'),
    ]

    operations = [
        migrations.RemoveField(
            model_name='config',
            name='config',
        ),
        migrations.AlterField(
            model_name='config',
            name='configname',
            field=zambia.models.ConfigConfig(unique=True, max_length=40, choices=[('CON_START_DATIM', 'CON_START_DATIM'), ('CON_NUM_DAYS', 'CON_NUM_DAYS'), ('DAY_CUTOFF_HOUR', 'DAY_CUTOFF_HOUR'), ('PREF_TTL_SESNS_LMT', 'PREF_TTL_SESNS_LMT'), ('PREF_DLY_SESNS_LMT', 'PREF_DLY_SESNS_LMT'), ('AVAILABILITY_ROWS', 'AVAILABILITY_ROWS'), ('MAX_BIO_LEN', 'MAX_BIO_LEN'), ('DURATION_IN_MINUTES', 'DURATION_IN_MINUTES'), ('MY_AVAIL_KIDS', 'MY_AVAIL_KIDS'), ('ENABLE_SHARE_EMAIL_QUESTION', 'ENABLE_SHARE_EMAIL_QUESTION'), ('ENABLE_BESTWAY_QUESTION', 'ENABLE_BESTWAY_QUESTION'), ('BILINGUAL', 'BILINGUAL'), ('SHOW_BRAINSTORM_LOGIN_HINT', 'SHOW_BRAINSTORM_LOGIN_HINT'), ('ANALYTICS_ENABLED', 'ANALYTICS_ENABLED'), ('CON_NAME', 'CON_NAME'), ('ADMIN_EMAIL', 'ADMIN_EMAIL'), ('BRAINSTORM_EMAIL', 'BRAINSTORM_EMAIL'), ('PROGRAM_EMAIL', 'PROGRAM_EMAIL'), ('REG_EMAIL', 'REG_EMAIL'), ('FIRST_DAY_START_TIME', 'FIRST_DAY_START_TIME'), ('OTHER_DAY_START_TIME', 'OTHER_DAY_START_TIME'), ('OTHER_DAY_STOP_TIME', 'OTHER_DAY_STOP_TIME'), ('LAST_DAY_STOP_TIME', 'LAST_DAY_STOP_TIME'), ('STANDARD_BLOCK_LENGTH', 'STANDARD_BLOCK_LENGTH'), ('DEFAULT_DURATION', 'DEFAULT_DURATION'), ('SMTP_ADDRESS', 'SMTP_ADDRESS'), ('SWIFT_DIRECTORY', 'SWIFT_DIRECTORY'), ('SECOND_LANG', 'SECOND_LANG'), ('SECOND_TITLE_CAPTION', 'SECOND_TITLE_CAPTION'), ('SECOND_DESCRIPTION_CAPTION', 'SECOND_DESCRIPTION_CAPTION'), ('SECOND_BIOGRAPHY_CAPTION', 'SECOND_BIOGRAPHY_CAPTION'), ('BASESESSIONDIR', 'BASESESSIONDIR'), ('CON_LOGO_IMAGE', 'CON_LOGO_IMAGE')]),
            preserve_default=False,
        ),
    ]
