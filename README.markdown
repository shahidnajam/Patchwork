#Patchwork

Patchwork is a combination of Zend Framework, PHPIDS and Doctrine and comes
ready to start a new ZF project.

* standard Zend Application and project structure
* input scanning using PHPIDS (using a controller plugin)
* Doctrine ORM
* Authentication against Doctrine + HTTP basic auth against Doctrine 
* JSON-REST service using Doctrine (use Patchwork_Controller_RESTModelController)
* shipped with a phing buildfile
* shipped with a blueprint css template for compass, see scripts/generate-css.sh

##Libraries:

Zend Framework 1.10.6 is used where all require-statements have been stripped
using the instruction from:
http://framework.zend.com/manual/en/performance.classloading.html#performance.classloading.striprequires

Doctrine 1.2.3 is included.

PHPIDS 0.6.4 is included.



##Installation

1) Make sure the directories required by PHPIDS are writeable.
2) Modify public/.htaccess to your needs
3) Modify application/configs/application.ini

##License

Patchwork is distributed under the MIT License.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Doctrine is published under the LGPL
http://www.doctrine-project.org

PHPIDS is released under the LGPL
http://code.google.com/p/phpids/

Zend Framework is released under the BSD license.
http://framework.zend.com/license

Patchwork contains code from ZFDoctrine, published under the BSD license
http://github.com/beberlei/zf-doctrine


##To Do

- integrate ZFDoctrine as gitmodule
- extend unit testing
- make scripts callable form everywhere
- user management module
- modular acl
- blueprint flash messenger view helper
- application firewall
- check: form csrf protection using zend hash element
- check: zend registry vs dependency injection container

##Not to do (Best practise - howto instead)

- escaping
- jquery view helpers

##Read more

PHING + PPHDocumentor
http://technosophos.com/category/tags/phing

Compass + SASS + Blueprint
http://sass-lang.com/
http://compass-style.org/
http://net.tutsplus.com/tutorials/html-css-techniques/using-compass-and-sass-for-css-in-your-next-project/
A blueprint cheat sheet is included in the doc directory.
