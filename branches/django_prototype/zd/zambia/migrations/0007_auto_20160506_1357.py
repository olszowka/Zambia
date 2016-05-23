# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0006_participantonsessionhistory'),
    ]

    operations = [
        migrations.AlterModelOptions(
            name='participantonsessionhistory',
            options={'managed': False, 'verbose_name': 'Participant on session history', 'verbose_name_plural': 'Participant on session histories'},
        ),
        migrations.AlterModelTable(
            name='participantonsessionhistory',
            table='ParticipantOnSessionHistory',
        ),
    ]
