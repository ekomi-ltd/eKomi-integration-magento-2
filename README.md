
# Official eKomi Plugin for Magento 2

## Description

eKomi Plugin for Magento2 allows you to integrate your Magento2 shop easily with the eKomi system. This allows you to collect verified reviews, display eKomi seal on your website and get your seller ratings on Google. This helps you increase your website's click-through rates, conversion rates and also, if you are running Google AdWord Campaigns, this helps in improving your Quality Score and hence your costs per click.

The eKomi product review container allows an easy integration of eKomi Reviews and ratings into your webshop. It allows you individual positioning of product reviews and includes the Google rich snippet functionality.

> Before installing and activating your plugin, please contact
> support@ekomi.de, this is necessary to ensure everything has been set
> up correctly and activated from eKomi’s side.

  

## Key features

- Collect order and/or product base Reviews

- Supports Simple, Configurable, Grouped and Bundle products

- Publish reviews on search engines: Google, Bing, Yahoo!

- Easy Integration with eKomi.

- Get Google Seller Ratings.

- Increase Clickthrough Rate by over 17%

- Increase Conversion Rate

- Manage Reviews: our team of Customer Feedback Managers, reviews each and every review for any terms which are not allowed and also put all negative reviews in moderation.

- Product total reviews

- Product avg reviews (star rating)

- List of reviews with pagination and sorting options

- Rating schema for google structured data

- The parent /child review display

  
  

## System Requirements

-   PHP version 5.0 or greater
    
-   MySQL version 5.6 or greater
    
-   Magento Version 2.x
    

  

## Installation

### 1. Module Installation (Composer)

**1.1** Login to the server via SSH and navigate to Magento root directory

**1.2** Execute the following commands respectively

    1.  composer config repositories.ekomi vcs https://github.com/ekomi-ltd/eKomi-integration-magento-2
    
    2.  composer require ekomiltd/ekomiintegration
        
    3.  php bin/magento setup:upgrade
        
    4.  php bin/magento setup:di:compile


**1.3** Refresh the Cache under System­ ⇾ Tools ⇾ Cache Management

**1.4** Navigate to **Stores­ ⇾ Settings ⇾ Configuration** and the module Ekomi Integration will be listed in the admin under Ekomi tab

![](https://lh4.googleusercontent.com/J-VmATNnZicCPiwahZoL8TxP-32zTqSV5InBGczvozVk4L47errzj7m7G3DF3ZhUbrK_VtbqRVJFnF_VoKNqWCDUAgZhOs7Un32onSvMaTSU8ZacodEdKQq4tN_F1WnA-eV1hkSe)

### 2. Module Installation (Manually)

**2.1** Download the extension and Unzip the files in a temporary directory

**2.2** Upload it to your Magento installation root directory

**2.3** Execute the following commands respectively:

    
    1.  php bin/magento module:enable Ekomi_EkomiIntegration --clear-static-content
    
    2.  php bin/magento setup:upgrade
        
    3.  php bin/magento setup:di:compile
    

**2.4** Refresh the Cache under System­ ⇾ Cache Management

**2.5** Navigate to **Stores­ ⇾ Settings ⇾ Configuration** and the module Ekomi Integration will be listed in the admin under Ekomi tab

![](https://lh4.googleusercontent.com/1046yZ1TKs5CTdZ3LCKypJt2SxVhuWfcxUiNCfP86VDUFb0mDial_KSJple4u-IG3A3D1WDAmzZ3UWGDFyPRYTNhBZNgGhG8EXh1B9Sx70xp0hLn2s-h8bLYBpfeGKaHnAWOetYY)

  

## Configuration

**1.** Navigate to **Stores ⇾ Settings ⇾ Configuration** and click on Ekomi Integration under EKOMI tab in the left panel.

![](https://lh3.googleusercontent.com/iS_FiAzCQ8uNp9srxWzV05zzEYPAOnmyl05yERccmA5l3gOOl1o29I6Wsn7-o1wLkgfjRRtVnfZi5dJxGEk1sgoe-iP0WtDPMHr5Jnh_Ky62F7Tjs5JSQCy73U_7v1iMJuf83kpr)

**2.** Configure eKomi Integration

![](https://lh4.googleusercontent.com/fAazQNmY4eU0TdvNgyNsRsX_fqFmEKDwx9zZg8dgSkzg4B2iU8UNJ4TmoNWvl4QZrEv_ld_Ppo1Cx8udJQzm4137Gxltrqb_ojhQnESHvT_3ammb-AgIeCYiK4o9d7XlVZwFfYnU)

 
-   Enabled: Set to Yes.
    
-   Product Base Reviews: If enabled, product attributes will also be sent to eKomi
    
-   i.e. Product ID, SKU, Name, Type, Image, and URL
 
-   Shop ID: Enter the Shop ID provided by eKomi.
    
-   Shop Password: Enter the Shop Password/Secret provided by eKomi.
    
-   Order Status: Choose Order Statuses on which you want to export order data to eKomi.
    
-   Review  Mode: for SMS, mobile number format should be according to E164.
    
-   Product Identifier: The attribute you want to use as the Product Identifier in the eKomi system.
    
-   Exclude Products: Comma separated product IDs/SKUs which should be excluded.
    
-   Terms and Conditions: If enabled, then sends order to eKomi System otherwise not.
    
-   Save the configuration

> Note: Plugin will only be enabled if shop credentials are valid.

**3.** Configure Product Review Container

![](https://lh4.googleusercontent.com/-cy3V9N4NoMxsJ2hx3XagEa_9Zq_9i4u0CPTdULhjuJlQ0Ot50obRrR8HPxTazqC_sSPdOgNdYeZAB88njWpIAAl1Tb_zXabHlm8fcIM9iOuq68NZhQlu-G-CsLD0u1AGDtSUvBS)

-   Show PRC: Set to Yes.
    
-   Widget Token: Enter the Widget Token provided by eKomi.
   
> Note: Ekomi Integration plugin should be enabled in order to display PRC.


## Display Product Review Container Widget

### 1. Admin Panel (Recommended)

1.1 Navigate to **Content ⇾ Widgets ⇾ Add Widget**


![mtwo2.png](https://lh4.googleusercontent.com/diZ5QCNR8MURfrVH1hrBsd-sZrF5HzH2OOO_9sxlXlOCoGZdYckDDLQp38_QNcJl82No-ko60Rl5J57f1QR3FX-sn4OuSFMW_rW6LvILImjpMJczJFa5rYIMYxwUFFpFP78AvAtN)

-   Type: Set eKomi Product Review Container
    
-   Design  Theme: Set the Current Theme
    
-   Press Continue
    

**1.2**  Storefront Properties

![Mtwo3.png](https://lh5.googleusercontent.com/kDJT2VCZomnGfQozwCAfPc_iOxpkb4QfQlbG_1nZoB9QaYiVifyOwK9oVIpksdHp4-dgzOEJNAUj8PwJAUBMg5mIofkHMlCBq5lmphuQbn4CwMKC-THWjMt36Yh7rHY74mBCXdT8)

-   Fill the meta information i.e. Widget Title, Store Views, and Sort Order
    

**1.3** Add Layout Updates

![](https://lh5.googleusercontent.com/1MsZaeqv2IXsqBfa75QlyBpe0TUmUbXv8UaDgBeO8G81JDURjLiUxJdKRS597ucgNidsNlU3j1Ucy0iXMoqDu2L1HpoWmOoS4uoarfWia7Gp29mV-0hHObW_fPmND95CRpYC8xYH)

  

-   Display On: Select desired product page
    
-   Products: Select required products if any.
    
-   Container: Select the required position for the widget.
    

**1.4** Save the Widget Instance and the PRC will be displayed on the Store Front



### 2. Programmatically

 
**2.1** Insert the following shortcode in the product detail page template where you want to display the widget.

 

    <?php echo $this->getLayout()
    
    ->createBlock('Ekomi\EkomiIntegration\Block\Widget\Reviews')
    
    ->setTemplate('Ekomi_EkomiIntegration::ekomi_sw_prc.phtml')
    
    ->toHtml();
    
    ?>

  
  
  
  
  
  
  

The PRC Widget will be displayed as:

  

![](https://lh3.googleusercontent.com/QxdvsFUG_5PPF47e_BDqiJ_I2JGAj_AISmr3WkAHCHhzaoo5oNIf18byUy5fp-y7rajlnwr0ag59UXZLb1_YzAQ9yAgIq-H0vCrLF_AHwA_N7zyQC1hghrgMQg1EKFk9RzoJYTD9)

  

## Troubleshooting

Our eKomi headquarters in Berlin is the best place to start if you need help with this plugin. There our technical support team will get you up and running in time. You can book assistance at: [http://ssi.ekomi.com/booking](http://ssi.ekomi.com/booking).

  
  
  

## Plugin information

· Maintenance status: Minimally maintained

· Development status: Stable

· Last modified: 2023-Jan-31

  

### Downloads

##### Recommended releases

 
| Version |    Download    |    Date     |
|---------|:--------------:|:-----------:|
| 1.1.0   | zip (11.3 kB)  | 2016-Oct-27 |
| 1.3.0   | zip (15.3 kB)  | 2018-Sep-01 |
| 2.0.0   | zip (20.1 kB)  | 2018-Nov-12 |
| 2.0.1   | zip (20.0 kB)  | 2018-Dec-04 |
| 2.1.0   | zip (20.8 kB)  | 2019-Jan-10 |
| 2.2.0   | zip (28.5 kB)  | 2019-Apr-30 |
| 2.2.1   | zip (29.4 kB)  | 2019-May-07 |
| 2.3.0   | zip (28.1 kB)  | 2019-May-22 |
| 2.3.1   | zip (28.1 kB)  | 2019-Sep-26 |
| 2.3.2   | zip (28.1 kB)  | 2019-Oct-02 |
| 2.3.3   | zip (28.1 kB)  | 2019-Dec-06 |
| 2.4.0   | zip (29.4 kB)  | 2020-Apr-02 |
| 2.5.0   | zip (29.5 kB)  | 2020-Jul-23 |
| 2.5.1   | zip (170.9 kB) | 2021-Feb-16 |
| 2.5.2   | zip (170.9 kB) | 2021-Mar-25 |
| 2.5.3   | zip (170.9 kB) | 2021-Mar-26 |
| 2.5.4   | zip (170.9 kB) | 2021-Mar-27 |
| 2.5.6   | zip (170.9 kB) | 2021-Mar-27 |
| 2.5.7   | zip (171.0 kB) | 2021-Apr-05 |
| 2.5.8   | zip (171.0 kB) | 2021-Jun-08 |
| 2.5.9   | zip (171.7 kB) | 2022-Jan-06 |
| 2.5.10  | zip (172.0 kB) | 2022-Sep-02 |
| 2.5.11  | zip (172.3 kB) | 2022-Dec-20 |
| 2.5.12  | zip (172.3 kB) | 2023-Jan-31 |

