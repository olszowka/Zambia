# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0007_auto_20160506_1357'),
    ]

    operations = [
        migrations.AlterModelOptions(
            name='participant',
            options={'managed': False, 'permissions': (('participant', 'Can access participant functionality'), ('public', 'Can view public functionality (ie brainstorm)'), ('brainstorm', 'Can make brainstorm changes'))},
        ),
    ]
