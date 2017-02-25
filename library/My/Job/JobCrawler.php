<?php

namespace My\Job;

use My\General;

class JobCrawler extends JobAbstract
{
    /*
     * crawler content from youtube
     */
    public function crawlerYoutube($params, $serviceLocator)
    {
        $file_success = __CLASS__ . '_' . __FUNCTION__ . '_' . 'Success';
        $file_error = __CLASS__ . '_' . __FUNCTION__ . '_' . 'Error';
        try {
            $instanceSearchContent = new \My\Search\Content();
            $google_config = General::$google_config;
            $client = new \Google_Client();
            $client->setDeveloperKey($google_config['key']);

            // Define an object that will be used to make all API requests.
            $youtube = new \Google_Service_YouTube($client);

            try {
                $arr_channel = [
                    '28' => [ //Videos Hài Hước
                        'UCwmurIyZ6FHyVPtKppe_2_A', //https://www.youtube.com/channel/UCwmurIyZ6FHyVPtKppe_2_A/videos -- DIEN QUAN Comedy / Hài
                        'UCFMEYTv6N64hIL9FlQ_hxBw', //https://www.youtube.com/channel/UCFMEYTv6N64hIL9FlQ_hxBw -- ĐÔNG TÂY PROMOTION OFFICIAL
                        'UCXarSb1YYXKAtcPQJECVH2Q', //https://www.youtube.com/channel/UCXarSb1YYXKAtcPQJECVH2Q -- Fan Nhã Phương Trường Giang
                        'UCruaM4824Rr_ry7fsD5Jwag', //https://www.youtube.com/channel/UCruaM4824Rr_ry7fsD5Jwag -- THVL Giải Trí
                        'UCQGd-eIAxQV7zMvTT4UmjZA', //https://www.youtube.com/channel/UCQGd-eIAxQV7zMvTT4UmjZA --Bánh Bao Bự
                        'UCPu7cX9LrVOlCDK905T9tVw', //https://www.youtube.com/channel/UCPu7cX9LrVOlCDK905T9tVw -- Kem Xôi TV
                        'UCq6ApdQI0roaprMAY1gZTgw', //https://www.youtube.com/channel/UCq6ApdQI0roaprMAY1gZTgw -- Ghiền Mì Gõ
                        'UCZ72vrOkYZmvs9c0XYQM8cA', //https://www.youtube.com/channel/UCZ72vrOkYZmvs9c0XYQM8cA -- TRƯỜNG GIANG FAN
                        'UC0jDoh3tVXCaqJ6oTve8ebA', //https://www.youtube.com/channel/UC0jDoh3tVXCaqJ6oTve8ebA -- FAP TV
                        'UCsluIbpgt14y6KUcwqCxXbg', //https://www.youtube.com/channel/UCsluIbpgt14y6KUcwqCxXbg -- MCVMedia
                        'UCp-yY0F1wgZ1CUnh3upZLBQ', //https://www.youtube.com/channel/UCp-yY0F1wgZ1CUnh3upZLBQ -- Trắng
                        'UC6K3k5O0Dogk1v00beoGMTw', //https://www.youtube.com/channel/UC6K3k5O0Dogk1v00beoGMTw -- POPSTVVIETNAM
                        'UCmTroavJBDcWhwptGyKkERA' //https://www.youtube.com/channel/UCmTroavJBDcWhwptGyKkERA -- PHIM CẤP 3
                    ],
                    '29' => [ //videos Trẻ Thơ
                        'UCBzZ9lJmcsPRbiyHPbLYvOA', //Elsa and Spiderman Compilations
                        'UC_lA07JiUMe-aNh-u-TxjHg', // https://www.youtube.com/channel/UC_lA07JiUMe-aNh-u-TxjHg -- TuLi TV
                        'UCMYCx114VbdhQtU_Tz9dndA', //https://www.youtube.com/channel/UCMYCx114VbdhQtU_Tz9dndA -- Come And Play
                        'UCAeYn3nppkt7469wYynZJCg', //https://www.youtube.com/channel/UCAeYn3nppkt7469wYynZJCg -- Spiderman Frozen Elsa & Friends
                        'UC0jJKKA_CD4q4QE5UBMPMrA', //https://www.youtube.com/channel/UC0jJKKA_CD4q4QE5UBMPMrA --ElsaSpidermanIRL
                        'UCIl8mU7DwcDZVgsVPbQ5U8A', //https://www.youtube.com/channel/UCIl8mU7DwcDZVgsVPbQ5U8A -- AnAn ToysReview TV
                        'UCXF15hEQI7y1hpoZK4cZDEQ', //https://www.youtube.com/channel/UCXF15hEQI7y1hpoZK4cZDEQ -- KN Channel
                        'UCKcQ7Jo2VAGHiPMfDwzeRUw', //https://www.youtube.com/channel/UCKcQ7Jo2VAGHiPMfDwzeRUw --ChuChuTV Surprise Eggs Toys
                        'UCCF-1DYSb2deBcsdypD4b-Q', //https://www.youtube.com/channel/UCCF-1DYSb2deBcsdypD4b-Q -- Spiderman Real Life Superhero
                        'UCSJsjCiTl2lourZXnigVCoA', //https://www.youtube.com/channel/UCSJsjCiTl2lourZXnigVCoA -- Thơ Nguyễn
                        'UC5ezaYrzZpyItPSRG27MLpg', //https://www.youtube.com/channel/UC5ezaYrzZpyItPSRG27MLpg -- POPS Kids
                        'UCm4tFSgINIb4yGty_R2by_Q' //https://www.youtube.com/channel/UCm4tFSgINIb4yGty_R2by_Q --- Bi Bi Bi
                    ],
                    '30' => [ //Videos Khoa Học - Khám Phá
                        'UCiZJtnTQunvoY0xgv-Ce_rg',
                        'UCrZU-Qjgjs75BvKrLIX5pXg', //https://www.youtube.com/channel/UCrZU-Qjgjs75BvKrLIX5pXg -- Làm Gì Đây
                        'UCVC7AzvC0r1iruRyOL5X3JA', //https://www.youtube.com/channel/UCVC7AzvC0r1iruRyOL5X3JA -- Bí Ẩn Lạ Kỳ
                        'UCIBhtklwSSnghOTi4AAtj-g', // https://www.youtube.com/channel/UCIBhtklwSSnghOTi4AAtj-g --CỖ MÁY
                        'UCoOxtz4PmB7AOP3nwPM_bEg', //https://www.youtube.com/channel/UCoOxtz4PmB7AOP3nwPM_bEg -- Săn bắt và hái lượm
                        'UChp7WB42sPXbFtr3e_g9CaA', //https://www.youtube.com/channel/UChp7WB42sPXbFtr3e_g9CaA -- Thế Giới Huyền Bí
                        'UCM4srUfoYLk0n21HqAa5bCA', //https://www.youtube.com/channel/UCM4srUfoYLk0n21HqAa5bCA -- Lê Thành Chung
                        'UC1a6kSwY-zOLpFqxMLImpZw' //https://www.youtube.com/channel/UC1a6kSwY-zOLpFqxMLImpZw -- Thiên nhiên kỳ thú

                    ],
                    '31' => [ //Videos Hot
                        'UCuSp9h4f73lXjiBE2J85i8g', //https://www.youtube.com/channel/UCuSp9h4f73lXjiBE2J85i8g -- Tin Nóng Trong Ngày
                        'UCID3GTmfIFmydRiCdH_Fjow', //https://www.youtube.com/channel/UCID3GTmfIFmydRiCdH_Fjow --Tổng Hợp
                        'UC92MnB1eOC47kaMPoHckBhA', //https://www.youtube.com/channel/UC92MnB1eOC47kaMPoHckBhA -- Tieu Phong
                        'UCrszDMN3snNmbqu8XHuOmIg', //https://www.youtube.com/channel/UCrszDMN3snNmbqu8XHuOmIg -- Tin Hot 19+
//                    'UCedhIhWwTYnoPvR-o62Oa8g' //https://www.youtube.com/channel/UCedhIhWwTYnoPvR-o62Oa8g -- VẹmTV News

                    ],
                    '32' => [ // Videos Ca Nhạc
                        'UCZq4u4hadohQDXO6ra8aubQ', //https://www.youtube.com/channel/UCZq4u4hadohQDXO6ra8aubQ -- VIVA Music
                        'UCF5RuEuoGrqGtscvLGLOMew', //https://www.youtube.com/channel/UCF5RuEuoGrqGtscvLGLOMew -- VIVA Shows
                        'UCUgXK2UjZ8G_EM438aYkGrw', //https://www.youtube.com/channel/UCUgXK2UjZ8G_EM438aYkGrw -POPS MUSIC
                        'UCFtat3KL0Z29ATKiYVrnBxw', //https://www.youtube.com/channel/UCFtat3KL0Z29ATKiYVrnBxw -- Hồng Ân Entertainment
                        'UCPtEZo-8wDgZlXIRg1jOoJA' //https://www.youtube.com/channel/UCPtEZo-8wDgZlXIRg1jOoJA -- MP3 Zing Official
                    ],
                    '33' => [ //Videos Phim
                        'UC5rqnUOQ6tm915MjlwO4G0g', //--Nam Việt TV
                        'UC_VPydo78uJetT1OYk4xbpw', //https://www.youtube.com/channel/UC_VPydo78uJetT1OYk4xbpw/playlists -- Hoàng Dương AOK
                        'UCOjHXJD_ZJbh8nsJIuNcxHw', // https://www.youtube.com/channel/UCOjHXJD_ZJbh8nsJIuNcxHw -- Phim Sắp Ra
                        'UCGk3yw5k_xQUS_KSDCC6Nhw', //https://www.youtube.com/channel/UCGk3yw5k_xQUS_KSDCC6Nhw/videos -- VTV - SUNRISE
                        'UCF3TM1yxDMdFm2p3h11j52Q' //https://www.youtube.com/channel/UCF3TM1yxDMdFm2p3h11j52Q -- Khoảnh khắc kỳ diệu
                    ],
                    '34' => [ //Videos Thể Thao
                        'UCmHs51bYwsNGMMDH6oMoF_A', //https://www.youtube.com/channel/UCmHs51bYwsNGMMDH6oMoF_A -- Football VN
                        'UCndcERoL9eG-XNljgUk1Gag', //https://www.youtube.com/channel/UCndcERoL9eG-XNljgUk1Gag -- VFF Channel
                        'UCXVmFKJdknhsl-Co6gUAhOA', //https://www.youtube.com/channel/UCXVmFKJdknhsl-Co6gUAhOA -- Captain Football VN
                        'UCZNoTFTsrWXA-dXElRm90bA' //https://www.youtube.com/channel/UCZNoTFTsrWXA-dXElRm90bA -- Hài Bóng Đá
                    ],
                    '27' => [//gamming
                        'UU2l8G7UE41Vaby59Dfg6r3w' //gamming
                    ]
                ];
                foreach ($arr_channel as $cate_id => $channels) {

                    foreach ($channels as $channel_id) {
                        $searchResponse = $youtube->search->listSearch(
                            'snippet', array(
                                'channelId' => $channel_id,
                                'maxResults' => 50
                            )
                        );

                        if (empty($searchResponse) || empty($searchResponse->getItems())) {
                            continue;
                        }

                        foreach ($searchResponse->getItems() as $item) {

                            if (empty($item) || empty($item->getSnippet())) {
                                continue;
                            }
                            $id = $item->getId()->getVideoId();

                            if (empty($id)) {
                                continue;
                            }

                            $title = $item->getSnippet()->getTitle();

                            if (empty($title)) {
                                continue;
                            }

                            $description = $item->getSnippet()->getDescription();

                            $arr_image = [];
                            foreach ($item->getSnippet()->getThumbnails()['modelData'] as $thumbnail) {
                                $size = $thumbnail['width'] . 'x' . $thumbnail['height'];
                                $arr_image[$size] = \My\General::crawlerImage($thumbnail['url'], $title, $size);
                            }

                            $is_exits = $instanceSearchContent->getDetail([
                                'cont_slug' => General::getSlug($title),
                                'status' => 1
                            ]);

                            if (!empty($is_exits)) {
                                echo \My\General::getColoredString("content title = {$title} is exits \n", 'red');
                                continue;
                            }

                            //crawler avatar
                            if (!empty($main_image)) {
                                $extension = end(explode('.', end(explode('/', $main_image))));
                                $name = General::getSlug($title) . '.' . $extension;
                                file_put_contents(STATIC_PATH . '/uploads/content/' . $name, General::crawler($main_image));
                                $main_image = STATIC_URL . '/uploads/content/' . $name;
                            }

                            $arr_data_content = [
                                'cont_title' => $title,
                                'cont_slug' => General::getSlug($title),
                                'cont_main_image' => $main_image,
                                'cont_detail' => html_entity_decode($description),
                                'created_date' => time(),
                                'user_created' => 1,
                                'cate_id' => $cate_id,
                                'cont_description' => $description ? $description : $title,
                                'cont_status' => 1,
                                'cont_views' => 0,
                                'method' => 'crawler',
                                'from_source' => $id,
                                'meta_keyword' => str_replace(' ', ',', $title),
                                'updated_date' => time()
                            ];

                            $serviceContent = $serviceLocator->get('My\Models\Content');
                            $id = $serviceContent->add($arr_data_content);
                            if ($id) {
                                $arr_data_content['cont_id'] = $id;

                                //giảm lượng chia sẻ lên facebook
                                if ($id % 2 == 0) {
                                    self::postToFb($arr_data_content);
                                }
                                echo \My\General::getColoredString("Crawler success 1 post id = {$id} \n", 'green');
                            } else {
                                echo \My\General::getColoredString("Can not insert content db", 'red');
                            }
                            unset($serviceContent);
                            unset($arr_data_content);
                            self::flush();
                            continue;
                        }
                    }
                }
                return true;
            } catch (\Exception $exc) {
                echo '<pre>';
                print_r($exc->getMessage());
                echo '</pre>';
                return true;
            }
            return true;
        } catch (\Exception $ex) {
            \My\General::writeLog($file_error, []);
        }
    }

    /*
    * Clear flush
    */
    static function flush()
    {
        ob_end_flush();
        ob_flush();
        flush();
    }
}
