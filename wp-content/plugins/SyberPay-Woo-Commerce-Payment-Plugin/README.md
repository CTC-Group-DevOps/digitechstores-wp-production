# Description 

SyberPay WooCommerce adds syberpay as a payment method in wooCommerce payment options. 

# Requirments 

Make sure WooCommerce plugin is installed first, if WooCommerce is not installed the plugin may cause some problems.

# Installation 
1. Upload SyberPay-WooCommerce to plugins directory or upload through wordpress plugins uploader as .zip file.
2. After installing activate the plugin.
3. Make sure to add Application, service, key and salt in SYberPay configurations options.
4. Make sure to add SyberPay  Syber Pay Get Payment URL and GET Payment status URLs.

# How to find SyberPay setting 

Go to WooCommerc > Settings > Checkout Tab > click SyberPay on Gateway display order list 

**Note :** make sure SyberPay is enabled.



# Configure WooCommerce notify and return Urls in SyberPay enviroments

## Notify url 

Notify url endpoint is  **/wc-api/wc_syberpay_gateway** .

For example adding notify url in configurations for http://www.example.com
notify url will be http://www.example.com/wc-api/wc_syberpay_gateway

## Return url

Retrun url endpoint **same as notify url**.