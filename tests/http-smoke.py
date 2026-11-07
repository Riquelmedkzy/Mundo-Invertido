"""Verifica rotas e proteções sem criar contas. python3 tests/http-smoke.py http://localhost/projinvertido"""
import sys,re,urllib.request,urllib.parse,urllib.error,http.cookiejar
base=(sys.argv[1] if len(sys.argv)>1 else 'http://127.0.0.1:8080').rstrip('/')
jar=http.cookiejar.CookieJar()
class NoRedirect(urllib.request.HTTPRedirectHandler):
 def redirect_request(self,*args):return None
client=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar),NoRedirect())
def request(query,body=None):
 data=None if body is None else urllib.parse.urlencode(body).encode()
 try:r=client.open(base+'/index.php?'+query,data=data)
 except urllib.error.HTTPError as e:r=e
 return r.code,r.headers,r.read().decode('utf-8')
def check(ok,name):
 print(('PASSOU' if ok else 'FALHOU')+': '+name)
 if not ok:sys.exit(1)
for route in ['home','portfolio','auth_login','register','contact','password_forgot']:
 code,headers,html=request('route='+route);check(code==200,route)
for route in ['../../etc/passwd','missing','home_bad']:
 code,_,html=request('route='+urllib.parse.quote(route));check(code==404 and 'root:x' not in html,'rota inválida '+route)
check(request('route[]=home')[0]==404,'rota em array')
check(request('route=register_add')[0]==405,'exige POST')
check(request('route=register_add',{'csrf':'invalid'})[0]==419,'CSRF inválido')
_,headers,html=request('route=home');token=re.search('name="csrf" value="([a-f0-9]+)"',html).group(1)
code,_,html=request('route=register_add',{'csrf':token,'name':'<script>alert(1)</script>','email':'invalid','phone':'x','password':'x'})
check(code==422 and '&lt;script&gt;alert(1)&lt;/script&gt;' in html and '<script>alert(1)</script>' not in html,'validação e escape XSS')
for route in ['profile','admin_users','admin_messages','admin_audit']:
 check(request('route='+route)[0]==303,'proteção '+route)
check("default-src 'self'" in headers.get('Content-Security-Policy',''),'CSP')
check(headers.get('X-Content-Type-Options')=='nosniff','nosniff')
