<?php
// ////////////////////////////////////////////////////////////////////////////
//
// Copyright (c) 2015-2016 Hangzhou Freewind Technology Co., Ltd.
// All rights reserved.
// http://www.seastart.cn
//
// ///////////////////////////////////////////////////////////////////////////
namespace common\util;

/**
 * 苹果内购
 *
 * @author Ather.Shu Sep 5, 2016 3:28:19 PM
 *        
 */
class AppleIapPay {

    private static $isSandbox = true;

    /**
     * 二次验证
     *
     * @param string $receipt transaction.transactionReceipt的base64串
     */
    public static function verify($receipt) {
        if( self::$isSandbox ) {
            $endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
        } else {
            $endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
        }
//         $receipt = "ewoJInNpZ25hdHVyZSIgPSAiQXhQa1RINENiT3hFR2dqT3UrQktHeUQ0T2xEejEzekY5M0xlWGVLelBIOFQ3VXNQaGZiOFlBMXV2cERyWFZHbnkvM3BqWTdGT3pwcm9iK0ptelhBNUN6bFY3WlVKTnpLMmkweVIwa0VNb2VlRGRoSlRrRml4NEhENkVTVE9NYnNIZTdRYW90TWVROVZobXFwVFV3SVkzRVdPNUZWU2VQRGZ3T25GeFZ6N08rV2EwM2ttdXJYdFZRVVBlL0pxS25UR21veWwzSDlkbERBOTJZdkNSY0FPOFhBdWJRQXBnUVdqZitka1pQQnVjRHFSeTVPWE5ITzRSSy92MVdOTFg2ZklpM054LytmMFNDM2J5cEx1eFpIRTQrdVpkSGN6Y2pSSUZwQThLMG9WOFYrWHhZNkxaM3lpTVRqVFFLRFBYOHRGelM1d1phNGozRXVZZFVaOUJKMzNNa0FBQVdBTUlJRmZEQ0NCR1NnQXdJQkFnSUlEdXRYaCtlZUNZMHdEUVlKS29aSWh2Y05BUUVGQlFBd2daWXhDekFKQmdOVkJBWVRBbFZUTVJNd0VRWURWUVFLREFwQmNIQnNaU0JKYm1NdU1Td3dLZ1lEVlFRTERDTkJjSEJzWlNCWGIzSnNaSGRwWkdVZ1JHVjJaV3h2Y0dWeUlGSmxiR0YwYVc5dWN6RkVNRUlHQTFVRUF3dzdRWEJ3YkdVZ1YyOXliR1IzYVdSbElFUmxkbVZzYjNCbGNpQlNaV3hoZEdsdmJuTWdRMlZ5ZEdsbWFXTmhkR2x2YmlCQmRYUm9iM0pwZEhrd0hoY05NVFV4TVRFek1ESXhOVEE1V2hjTk1qTXdNakEzTWpFME9EUTNXakNCaVRFM01EVUdBMVVFQXd3dVRXRmpJRUZ3Y0NCVGRHOXlaU0JoYm1RZ2FWUjFibVZ6SUZOMGIzSmxJRkpsWTJWcGNIUWdVMmxuYm1sdVp6RXNNQ29HQTFVRUN3d2pRWEJ3YkdVZ1YyOXliR1IzYVdSbElFUmxkbVZzYjNCbGNpQlNaV3hoZEdsdmJuTXhFekFSQmdOVkJBb01Da0Z3Y0d4bElFbHVZeTR4Q3pBSkJnTlZCQVlUQWxWVE1JSUJJakFOQmdrcWhraUc5dzBCQVFFRkFBT0NBUThBTUlJQkNnS0NBUUVBcGMrQi9TV2lnVnZXaCswajJqTWNqdUlqd0tYRUpzczl4cC9zU2cxVmh2K2tBdGVYeWpsVWJYMS9zbFFZbmNRc1VuR09aSHVDem9tNlNkWUk1YlNJY2M4L1cwWXV4c1FkdUFPcFdLSUVQaUY0MWR1MzBJNFNqWU5NV3lwb041UEM4cjBleE5LaERFcFlVcXNTNCszZEg1Z1ZrRFV0d3N3U3lvMUlnZmRZZUZScjZJd3hOaDlLQmd4SFZQTTNrTGl5a29sOVg2U0ZTdUhBbk9DNnBMdUNsMlAwSzVQQi9UNXZ5c0gxUEttUFVockFKUXAyRHQ3K21mNy93bXYxVzE2c2MxRkpDRmFKekVPUXpJNkJBdENnbDdaY3NhRnBhWWVRRUdnbUpqbTRIUkJ6c0FwZHhYUFEzM1k3MkMzWmlCN2o3QWZQNG83UTAvb21WWUh2NGdOSkl3SURBUUFCbzRJQjF6Q0NBZE13UHdZSUt3WUJCUVVIQVFFRU16QXhNQzhHQ0NzR0FRVUZCekFCaGlOb2RIUndPaTh2YjJOemNDNWhjSEJzWlM1amIyMHZiMk56Y0RBekxYZDNaSEl3TkRBZEJnTlZIUTRFRmdRVWthU2MvTVIydDUrZ2l2Uk45WTgyWGUwckJJVXdEQVlEVlIwVEFRSC9CQUl3QURBZkJnTlZIU01FR0RBV2dCU0lKeGNKcWJZWVlJdnM2N3IyUjFuRlVsU2p0ekNDQVI0R0ExVWRJQVNDQVJVd2dnRVJNSUlCRFFZS0tvWklodmRqWkFVR0FUQ0IvakNCd3dZSUt3WUJCUVVIQWdJd2diWU1nYk5TWld4cFlXNWpaU0J2YmlCMGFHbHpJR05sY25ScFptbGpZWFJsSUdKNUlHRnVlU0J3WVhKMGVTQmhjM04xYldWeklHRmpZMlZ3ZEdGdVkyVWdiMllnZEdobElIUm9aVzRnWVhCd2JHbGpZV0pzWlNCemRHRnVaR0Z5WkNCMFpYSnRjeUJoYm1RZ1kyOXVaR2wwYVc5dWN5QnZaaUIxYzJVc0lHTmxjblJwWm1sallYUmxJSEJ2YkdsamVTQmhibVFnWTJWeWRHbG1hV05oZEdsdmJpQndjbUZqZEdsalpTQnpkR0YwWlcxbGJuUnpMakEyQmdnckJnRUZCUWNDQVJZcWFIUjBjRG92TDNkM2R5NWhjSEJzWlM1amIyMHZZMlZ5ZEdsbWFXTmhkR1ZoZFhSb2IzSnBkSGt2TUE0R0ExVWREd0VCL3dRRUF3SUhnREFRQmdvcWhraUc5Mk5rQmdzQkJBSUZBREFOQmdrcWhraUc5dzBCQVFVRkFBT0NBUUVBRGFZYjB5NDk0MXNyQjI1Q2xtelQ2SXhETUlKZjRGelJqYjY5RDcwYS9DV1MyNHlGdzRCWjMrUGkxeTRGRkt3TjI3YTQvdncxTG56THJSZHJqbjhmNUhlNXNXZVZ0Qk5lcGhtR2R2aGFJSlhuWTR3UGMvem83Y1lmcnBuNFpVaGNvT0FvT3NBUU55MjVvQVE1SDNPNXlBWDk4dDUvR2lvcWJpc0IvS0FnWE5ucmZTZW1NL2oxbU9DK1JOdXhUR2Y4YmdwUHllSUdxTktYODZlT2ExR2lXb1IxWmRFV0JHTGp3Vi8xQ0tuUGFObVNBTW5CakxQNGpRQmt1bGhnd0h5dmozWEthYmxiS3RZZGFHNllRdlZNcHpjWm04dzdISG9aUS9PamJiOUlZQVlNTnBJcjdONFl0UkhhTFNQUWp2eWdhWndYRzU2QWV6bEhSVEJoTDhjVHFBPT0iOwoJInB1cmNoYXNlLWluZm8iID0gImV3b0pJbTl5YVdkcGJtRnNMWEIxY21Ob1lYTmxMV1JoZEdVdGNITjBJaUE5SUNJeU1ERTJMVEE1TFRBMUlEQXlPakF5T2pJeUlFRnRaWEpwWTJFdlRHOXpYMEZ1WjJWc1pYTWlPd29KSW5WdWFYRjFaUzFwWkdWdWRHbG1hV1Z5SWlBOUlDSTBNVGRoTURJNFlXSmxaRFprT1dSbVpXUXlNVEl3TVRkbE5EYzVaVEptT1RNek4ySXhOR1pqSWpzS0NTSnZjbWxuYVc1aGJDMTBjbUZ1YzJGamRHbHZiaTFwWkNJZ1BTQWlNVEF3TURBd01ESXpNemcxTVRRNE55STdDZ2tpWW5aeWN5SWdQU0FpTVM0d0xqRWlPd29KSW5SeVlXNXpZV04wYVc5dUxXbGtJaUE5SUNJeE1EQXdNREF3TWpNek9EVXhORGczSWpzS0NTSnhkV0Z1ZEdsMGVTSWdQU0FpTVNJN0Nna2liM0pwWjJsdVlXd3RjSFZ5WTJoaGMyVXRaR0YwWlMxdGN5SWdQU0FpTVRRM016QTJOakUwTWpneU1DSTdDZ2tpZFc1cGNYVmxMWFpsYm1SdmNpMXBaR1Z1ZEdsbWFXVnlJaUE5SUNJNVFVUkZNekZEUVMwek1FTXhMVFJFUVRRdFFUSTVPQzB3T1VJelJrSkZNRE0zUmtJaU93b0pJbkJ5YjJSMVkzUXRhV1FpSUQwZ0ltTnZiUzVpWW1WcGRuSXVZWEJ3TkNJN0Nna2lhWFJsYlMxcFpDSWdQU0FpTVRFME9Ea3hNREkyTmlJN0Nna2lZbWxrSWlBOUlDSmpiMjB1WW1KbGFYWnlMbUZ3Y0NJN0Nna2ljSFZ5WTJoaGMyVXRaR0YwWlMxdGN5SWdQU0FpTVRRM016QTJOakUwTWpneU1DSTdDZ2tpY0hWeVkyaGhjMlV0WkdGMFpTSWdQU0FpTWpBeE5pMHdPUzB3TlNBd09Ub3dNam95TWlCRmRHTXZSMDFVSWpzS0NTSndkWEpqYUdGelpTMWtZWFJsTFhCemRDSWdQU0FpTWpBeE5pMHdPUzB3TlNBd01qb3dNam95TWlCQmJXVnlhV05oTDB4dmMxOUJibWRsYkdWeklqc0tDU0p2Y21sbmFXNWhiQzF3ZFhKamFHRnpaUzFrWVhSbElpQTlJQ0l5TURFMkxUQTVMVEExSURBNU9qQXlPakl5SUVWMFl5OUhUVlFpT3dwOSI7CgkiZW52aXJvbm1lbnQiID0gIlNhbmRib3giOwoJInBvZCIgPSAiMTAwIjsKCSJzaWduaW5nLXN0YXR1cyIgPSAiMCI7Cn0=";
        $postData = '{"receipt-data":"' . $receipt . '"}';
        
        $ch = curl_init( $endpoint );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 ); // 这两行一定要加，不加会报SSL 错误
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        
        $response = curl_exec( $ch );
        $errno = curl_errno( $ch );
        $errmsg = curl_error( $ch );
        curl_close( $ch );
        // 判断时候出错，抛出异常
        if( $errno != 0 ) {
            throw new \Exception( $errmsg, $errno );
        }
        
        $data = json_decode( $response, true );
        // {
        // "receipt": {
//                 "original_purchase_date_pst":"2015-06-03 04:00:37 America/Los_Angeles",
//                 "purchase_date_ms":"1433329237329",
//                 "unique_identifier":"secret9f135e2cd8f7dda951a15c01cd2220c60b",
//                 "original_transaction_id":"1000000157783770",
//                 "bvrs":"2.6.0",
//                 "transaction_id":"1000000157783770",
//                 "quantity":"1",
//                 "unique_vendor_identifier":"SECRETCD-89AD-45C4-8937-359CCA9E8F36",
//                 "item_id":"SECRET509",
//                 "product_id":"com.your.iap.product.id",
//                 "purchase_date":"2015-06-03 11:00:37 Etc/GMT",
//                 "original_purchase_date":"2015-06-03 11:00:37 Etc/GMT",
//                 "purchase_date_pst":"2015-06-03 04:00:37 America/Los_Angeles",
//                 "bid":"com.your.app.bundle.id",
//                 "original_purchase_date_ms":"1433329237329"
        // },
        // "status": 0
        // }
        // 判断返回的数据是否是对象
        if( !is_array( $data ) ) {
            throw new \Exception( 'Invalid response data' );
        }
        // 判断购买时候成功
        if( !isset( $data ['status'] ) || $data ['status'] != 0 ) {
            throw new \Exception( 'Invalid receipt：' . $data ['status'] );
        }
        
        // 返回产品的信息
        return $data ['receipt'];
    }
}