<?php
namespace CommerIQ\Helpers;

defined('ABSPATH') || exit;

class Utils
{
    public static function derive_from_woocommerce()
    {
        $out = ['country' => '', 'currency' => '', 'state' => '', 'address_1' => '', 'address_2' => ''];
        // Try to use WooCommerce API when available (more authoritative)
        if (function_exists('get_option')) {
            $out['address_1'] = trim((string) get_option('woocommerce_store_address', ''));
            $out['address_2'] = trim((string) get_option('woocommerce_store_address_2', ''));

            $default_country = get_option('woocommerce_default_country', '');
            if (!empty($default_country)) {
                $parts = explode(':', $default_country);
                $out['country'] = isset($parts[0]) ? trim($parts[0]) : '';
                $out['state'] = isset($parts[1]) ? trim($parts[1]) : '';
            }

            $out['currency'] = trim((string) get_option('woocommerce_currency', ''));
        }

        // Try to use WooCommerce API when available (more authoritative)
        if ((function_exists('WC') || class_exists('WC') || class_exists('WooCommerce'))) {
            try {
                if (function_exists('WC')) {
                    $wc = WC();
                    // Country/state via WC countries API
                    if (!empty($wc) && isset($wc->countries)) {
                        $base_country = method_exists($wc->countries, 'get_base_country') ? $wc->countries->get_base_country() : '';
                        $base_state = method_exists($wc->countries, 'get_base_state') ? $wc->countries->get_base_state() : '';
                        $country_code = !empty($base_country) ? trim((string) $base_country) : '';
                        $state_code = !empty($base_state) ? trim((string) $base_state) : '';

                        // Resolve to human-readable labels if possible
                        try {
                            $countries = method_exists($wc->countries, 'get_countries') ? $wc->countries->get_countries() : [];
                            if (!empty($country_code) && isset($countries[$country_code])) {
                                $out['country'] = $countries[$country_code];
                            } else {
                                $out['country'] = $country_code;
                            }

                            if (!empty($country_code) && !empty($state_code) && method_exists($wc->countries, 'get_states')) {
                                $states = $wc->countries->get_states($country_code);
                                if (isset($states[$state_code])) {
                                    $out['state'] = $states[$state_code];
                                } else {
                                    $out['state'] = $state_code;
                                }
                            } else {
                                $out['state'] = $state_code;
                            }
                        } catch (\Exception $e) {
                            $out['country'] = $country_code;
                            $out['state'] = $state_code;
                        }

                        // Currency
                        if (function_exists('get_woocommerce_currency')) {
                            $wc_currency = get_woocommerce_currency();
                            $out['currency'] = !empty($wc_currency) ? trim((string) $wc_currency) : '';
                        } else {
                            $out['currency'] = trim((string) get_option('woocommerce_currency', ''));
                        }
                    }
                }
            } catch (\Exception $e) {
                // ignore and continue
            }
        }

        return $out;
    }
}
