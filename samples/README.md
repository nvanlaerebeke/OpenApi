# OpenApi-Samples


Sample & tutorial list:

- [OpenApi-Samples Basic] [2]
- [OpenApi-Samples Versioning] [4]
- [OpenApi-Samples Auth] [5]
- [OpenApi-Samples Validation] [7]
- [OpenApi-Samples Error Handling] [3]
- [OpenApi-Samples REST Routing] [6]
- [OpenApi-Samples Database Find Method] [8]
- [OpenApi-Samples All In One] [9]

  [1]: https://github.com/nvanlaerebeke/OpenApi-Samples                                 "OpenApi-Samples"
  [2]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Basic               "OpenApi-Samples Basic"
  [3]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Error%20Handling    "OpenApi-Samples ErrorHandling"
  [4]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Versioning          "OpenApi-Samples Versioning"
  [5]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Auth                "OpenApi-Samples Auth"
  [6]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/REST%20Routing      "OpenApi-Samples REST Routing"
  [7]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Validation          "OpenApi-Samples Validation"
  [8]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/DatabaseFindMethod  "OpenApi-Samples Database Find Method"
  [9]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/AllInOne            "OpenApi-Samples All In One"


## Installation

To get the samples up and running check out the OpenApi Samples somewhere on your drive and link browse to the webroot.

I'll give a linux install as example as that's the easiest one to get working:

```    
cd /usr/local/ 
git clone git://github.com/nvanlaerebeke/OpenApi-Samples.git
ln -s /usr/local/OpenApi-Samples/Basic/webroot/ /var/www/html/basic
``` 

When going to "http://<yourhost>/basic"  you'll now be able to see the basic sample.

If you want to get the components separately the plugin can be found here:
 - https://github.com/nvanlaerebeke/OpenApi
 
The CakePHP framework can be found here:
 - https://github.com/cakephp/cakephp
 
The directories under OpenApi samples are all separate app's so you can do with them what you want.