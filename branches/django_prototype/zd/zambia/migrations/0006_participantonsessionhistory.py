# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0005_auto_20160504_2051'),
    ]

    operations = [
        migrations.CreateModel(
            name='ParticipantOnSessionHistory',
            fields=[
                ('participantonsessionhistoryid', models.AutoField(serialize=False, primary_key=True)),
                ('moderator', models.IntegerField(null=True, blank=True)),
                ('createdts', models.DateTimeField()),
                ('createdbybadge', models.CharField(max_length=15)),
                ('inactivatedts', models.DateTimeField()),
                ('inactivatedbybadge', models.CharField(max_length=15, null=True, blank=True)),
            ],
            options={
                'db_table': 'ParticipantOnSession',
                'managed': False,
            },
        ),
    ]
