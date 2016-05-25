# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0009_auto_20160521_2142'),
    ]

    operations = [
        migrations.CreateModel(
            name='Config',
            fields=[
                ('configid', models.AutoField(serialize=False, primary_key=True)),
                ('config', models.CharField(unique=True, max_length=40)),
                ('value', models.CharField(max_length=255)),
            ],
        ),
    ]
