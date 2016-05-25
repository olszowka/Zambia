# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0014_auto_20160525_1444'),
    ]

    operations = [
        migrations.RenameField(
            model_name='config',
            old_name='description',
            new_name='configdescription',
        ),
    ]
