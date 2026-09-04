<?php

namespace App\Constants;

class RouteConstants
{
    // =========================================================================
    // PUBLIC STOREFRONT ROUTES
    // =========================================================================
    public const PATH_HOME = '/';
    public const NAME_HOME = 'home';

    public const PATH_SHOP = '/shop';
    public const NAME_SHOP_INDEX = 'shop.index';

    public const PATH_SHOP_SHOW = '/shop/{product}';
    public const NAME_SHOP_SHOW = 'shop.show';

    // =========================================================================
    // CART ROUTES
    // =========================================================================
    public const PREFIX_CART = 'cart';
    public const NAME_CART_GROUP = 'cart.';
    
    public const PATH_CART_INDEX = '/';
    public const NAME_CART_INDEX = 'cart.index';

    public const PATH_CART_ADD = '/add';
    public const NAME_CART_ADD = 'cart.add';

    public const PATH_CART_UPDATE = '/update/{id}';
    public const NAME_CART_UPDATE = 'cart.update';

    public const PATH_CART_REMOVE = '/remove/{id}';
    public const NAME_CART_REMOVE = 'cart.remove';

    public const PATH_CART_BULK_REMOVE = '/bulk-remove';
    public const NAME_CART_BULK_REMOVE = 'cart.bulk_remove';

    public const PATH_CART_COUNT = '/count';
    public const NAME_CART_COUNT = 'cart.count';

    // =========================================================================
    // CHECKOUT ROUTES
    // =========================================================================
    public const PREFIX_CHECKOUT = 'checkout';
    public const NAME_CHECKOUT_GROUP = 'checkout.';

    public const PATH_CHECKOUT_INDEX = '/';
    public const NAME_CHECKOUT_INDEX = 'checkout.index';

    public const PATH_CHECKOUT_PROCESS = '/';
    public const NAME_CHECKOUT_PROCESS = 'checkout.process';

    public const PATH_CHECKOUT_SUCCESS = '/success/{order}';
    public const NAME_CHECKOUT_SUCCESS = 'checkout.success';

    // =========================================================================
    // AUTHENTICATION ROUTES
    // =========================================================================
    public const PATH_LOGIN = 'login';
    public const NAME_LOGIN = 'login';

    public const PATH_REGISTER = 'register';
    public const NAME_REGISTER = 'register';

    public const PATH_LOGOUT = 'logout';
    public const NAME_LOGOUT = 'logout';

    // =========================================================================
    // EMAIL VERIFICATION ROUTES
    // =========================================================================
    public const PATH_VERIFICATION_NOTICE = '/email/verify';
    public const NAME_VERIFICATION_NOTICE = 'verification.notice';

    public const PATH_VERIFICATION_VERIFY = '/email/verify/{id}/{hash}';
    public const NAME_VERIFICATION_VERIFY = 'verification.verify';

    public const PATH_VERIFICATION_SEND = '/email/verification-notification';
    public const NAME_VERIFICATION_SEND = 'verification.send';

    // =========================================================================
    // ADDRESS BOOK ROUTES
    // =========================================================================
    public const PREFIX_ADDRESSES = 'addresses';
    public const NAME_ADDRESSES_GROUP = 'addresses.';

    public const PATH_ADDRESSES_INDEX = '/';
    public const NAME_ADDRESSES_INDEX = 'addresses.index';

    public const PATH_ADDRESSES_STORE = '/';
    public const NAME_ADDRESSES_STORE = 'addresses.store';

    public const PATH_ADDRESSES_UPDATE = '/{address}';
    public const NAME_ADDRESSES_UPDATE = 'addresses.update';

    public const PATH_ADDRESSES_DESTROY = '/{address}';
    public const NAME_ADDRESSES_DESTROY = 'addresses.destroy';

    public const PATH_ADDRESSES_SET_DEFAULT = '/{address}/set-default';
    public const NAME_ADDRESSES_SET_DEFAULT = 'addresses.set_default';

    // =========================================================================
    // USER ACCOUNT ROUTES
    // =========================================================================
    public const PREFIX_ACCOUNT = 'account';
    public const NAME_ACCOUNT_GROUP = 'account.';

    public const PATH_ACCOUNT_ORDERS = '/orders';
    public const NAME_ACCOUNT_ORDERS = 'account.orders';

    public const PATH_ACCOUNT_ORDER_SHOW = '/orders/{order}';
    public const NAME_ACCOUNT_ORDER_SHOW = 'account.orders.show';

    public const PATH_ACCOUNT_ORDERS_CANCEL = '/orders/{order}/cancel';
    public const NAME_ACCOUNT_ORDERS_CANCEL = 'account.orders.cancel';

    public const PATH_ACCOUNT_ORDERS_REORDER = '/orders/{order}/reorder';
    public const NAME_ACCOUNT_ORDERS_REORDER = 'account.orders.reorder';

    public const PATH_ACCOUNT_ADDRESSES = '/addresses';
    public const NAME_ACCOUNT_ADDRESSES = 'account.addresses';

    public const PATH_ACCOUNT_COINS = '/coins';
    public const NAME_ACCOUNT_COINS = 'account.coins';

    // =========================================================================
    // REVIEW & COIN ROUTES
    // =========================================================================
    public const PATH_REVIEWS_STORE = '/reviews';
    public const NAME_REVIEWS_STORE = 'reviews.store';

    public const PATH_REVIEWS_FILTER = '/products/{product}/reviews';
    public const NAME_REVIEWS_FILTER = 'products.reviews.filter';

    public const PATH_COINS_CALCULATE = '/checkout/calculate-coins';
    public const NAME_COINS_CALCULATE = 'checkout.calculate_coins';

    // =========================================================================
    // ADMIN ROUTES
    // =========================================================================
    public const PREFIX_ADMIN = 'admin';
    public const NAME_ADMIN_GROUP = 'admin.';

    public const PATH_ADMIN_DASHBOARD = '/dashboard';
    public const NAME_ADMIN_DASHBOARD = 'admin.dashboard';

    public const RESOURCE_PRODUCTS = 'products';
    public const RESOURCE_CATEGORIES = 'categories';
    public const RESOURCE_BRANDS = 'brands';
    public const RESOURCE_USERS = 'users';
    public const RESOURCE_ORDERS = 'orders';
    public const RESOURCE_VOUCHERS = 'vouchers';
    public const RESOURCE_REVIEWS = 'reviews';

    public const NAME_ADMIN_PRODUCTS_INDEX = 'admin.products.index';
    public const NAME_ADMIN_PRODUCTS_CREATE = 'admin.products.create';
    public const NAME_ADMIN_PRODUCTS_STORE = 'admin.products.store';
    public const NAME_ADMIN_PRODUCTS_SHOW = 'admin.products.show';
    public const NAME_ADMIN_PRODUCTS_EDIT = 'admin.products.edit';
    public const NAME_ADMIN_PRODUCTS_UPDATE = 'admin.products.update';
    public const NAME_ADMIN_PRODUCTS_DESTROY = 'admin.products.destroy';

    public const NAME_ADMIN_CATEGORIES_INDEX = 'admin.categories.index';
    public const NAME_ADMIN_BRANDS_INDEX = 'admin.brands.index';
    public const NAME_ADMIN_USERS_INDEX = 'admin.users.index';

    public const NAME_ADMIN_ORDERS_INDEX = 'admin.orders.index';
    public const NAME_ADMIN_ORDERS_SHOW = 'admin.orders.show';
    public const NAME_ADMIN_ORDERS_UPDATE_STATUS = 'admin.orders.update_status';
    public const NAME_ADMIN_ORDERS_SEND_GHN = 'admin.orders.send_ghn';
    public const NAME_ADMIN_ORDERS_CANCEL = 'admin.orders.cancel';
    public const NAME_ADMIN_ORDERS_PRINT_LABEL = 'admin.orders.print_label';

    public const NAME_ADMIN_VOUCHERS_INDEX = 'admin.vouchers.index';
    public const NAME_ADMIN_VOUCHERS_CREATE = 'admin.vouchers.create';
    public const NAME_ADMIN_VOUCHERS_STORE = 'admin.vouchers.store';
    public const NAME_ADMIN_VOUCHERS_SHOW = 'admin.vouchers.show';
    public const NAME_ADMIN_VOUCHERS_EDIT = 'admin.vouchers.edit';
    public const NAME_ADMIN_VOUCHERS_UPDATE = 'admin.vouchers.update';
    public const NAME_ADMIN_VOUCHERS_DESTROY = 'admin.vouchers.destroy';
    public const NAME_ADMIN_VOUCHERS_TOGGLE = 'admin.vouchers.toggle';

    public const NAME_ADMIN_REVIEWS_INDEX = 'admin.reviews.index';
    public const NAME_ADMIN_REVIEWS_REPLY = 'admin.reviews.reply';
    public const NAME_ADMIN_REVIEWS_TOGGLE = 'admin.reviews.toggle';
}
