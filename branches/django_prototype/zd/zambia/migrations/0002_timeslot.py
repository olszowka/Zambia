# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0001_initial'),
    ]

    operations = [
        migrations.CreateModel(
            name='TimeSlot',
            fields=[
                ('timeid', models.IntegerField(serialize=False, primary_key=True)),
                ('timedisplay', models.CharField(max_length=14, null=True, blank=True)),
                ('timevalue', models.TimeField(null=True, blank=True)),
                ('next_day', models.IntegerField(null=True, blank=True)),
                ('display_order', models.IntegerField(null=True, blank=True)),
                ('avail_start', models.IntegerField(null=True, blank=True)),
                ('avail_end', models.IntegerField(null=True, blank=True)),
            ],
            options={
                'db_table': 'Times',
                'managed': False,
            },
        ),
    ]
