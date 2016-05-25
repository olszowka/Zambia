import re
class PrefixMiddleware(object):
    def __init__(self, app, prefix='/'):
        self.app = app
        self.prefix = prefix.rstrip('/')
        self.regprefix = re.compile("^%s(.*)$" % self.prefix)
    def __call__(self, environ, start_response):
        url = environ['PATH_INFO']
        url = re.sub(self.regprefix, r'\1', url)
        if not url:
            url = '/'
        environ['PATH_INFO'] = url
        environ['SCRIPT_NAME'] = self.prefix
        return self.app(environ, start_response)
