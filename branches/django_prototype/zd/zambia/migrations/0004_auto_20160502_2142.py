# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0003_auto_20160502_1626'),
    ]

    operations = [
        migrations.AlterModelOptions(
            name='categoryhasreport',
            options={'ordering': ['reportcategory', 'reporttype'], 'managed': False},
        ),
        migrations.AlterModelOptions(
            name='reportcategory',
            options={'ordering': ['display_order'], 'managed': False, 'verbose_name': 'Report category', 'verbose_name_plural': 'Report categories'},
        ),
        migrations.AlterModelOptions(
            name='reporttype',
            options={'ordering': ['title'], 'managed': False},
        ),
    ]
