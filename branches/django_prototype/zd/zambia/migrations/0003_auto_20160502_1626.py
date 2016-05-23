# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0002_timeslot'),
    ]

    operations = [
        migrations.AlterModelOptions(
            name='bioeditstatus',
            options={'managed': False, 'verbose_name': 'Bio edit status', 'verbose_name_plural': 'Bio edit statuses'},
        ),
        migrations.AlterModelOptions(
            name='kidscategory',
            options={'managed': False, 'verbose_name': 'Kids category', 'verbose_name_plural': 'Kids categories'},
        ),
        migrations.AlterModelOptions(
            name='languagestatus',
            options={'managed': False, 'verbose_name': 'Language status', 'verbose_name_plural': 'Language statuses'},
        ),
        migrations.AlterModelOptions(
            name='pubstatus',
            options={'managed': False, 'verbose_name': 'Pub status', 'verbose_name_plural': 'Pub statuses'},
        ),
        migrations.AlterModelOptions(
            name='reportcategory',
            options={'managed': False, 'verbose_name': 'Report category', 'verbose_name_plural': 'Report categories'},
        ),
        migrations.AlterModelOptions(
            name='reportquery',
            options={'managed': False, 'verbose_name': 'Report query', 'verbose_name_plural': 'Report queries'},
        ),
        migrations.AlterModelOptions(
            name='sessionstatus',
            options={'managed': False, 'verbose_name': 'Session status', 'verbose_name_plural': 'Session statuses'},
        ),
    ]
