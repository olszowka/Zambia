# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0010_config'),
    ]

    operations = [
        migrations.AddField(
            model_name='config',
            name='description',
            field=models.TextField(default='FEED ME'),
            preserve_default=False,
        ),
    ]
