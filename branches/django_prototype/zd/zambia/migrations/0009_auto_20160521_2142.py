# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0008_auto_20160520_1754'),
    ]

    operations = [
        migrations.AlterModelOptions(
            name='participant',
            options={'managed': False, 'permissions': (('participant', 'Can access participant functionality'), ('public', 'Can view public functionality (ie brainstorm)'), ('brainstorm', 'Can make brainstorm changes'), ('brainstorm_search', 'Can search brainstorm sessions'))},
        ),
    ]
