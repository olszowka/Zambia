# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0004_auto_20160502_2142'),
    ]

    operations = [
        migrations.AlterModelOptions(
            name='sessionedithistory',
            options={'managed': False, 'verbose_name': 'Session edit history', 'verbose_name_plural': 'Session edit histories'},
        ),
        migrations.AlterModelOptions(
            name='trackcompatibility',
            options={'managed': False, 'verbose_name': 'Track compatibility', 'verbose_name_plural': 'Track compatibilities'},
        ),
    ]
