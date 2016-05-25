# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import migrations, models

def fix_configname(apps, schema_editor):
    MyModel = apps.get_model('zambia', 'Config')
    for row in MyModel.objects.all():
        row.configname = row.config
        row.save()

class Migration(migrations.Migration):

    dependencies = [
        ('zambia', '0012_auto_20160525_1427'),
    ]

    operations = [
        migrations.RunPython(fix_configname, reverse_code=migrations.RunPython.noop),
    ]
