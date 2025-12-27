<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPageSection;
use Illuminate\Support\Facades\DB;

class LandingPageSectionSeeder extends Seeder
{
    public function run(): void
    {
        // Use delete instead of truncate to work within transactions
        DB::table('landing_page_sections')->delete();

        $sections = [
            // Hero Section
            [
                'section_key' => 'hero',
                'title_ar' => 'مون شيف - احجز طاهيك المفضل',
                'title_en' => 'Moon Chef - Book Your Favorite Chef',
                'description_ar' => 'منصة تربط الطهاة المحترفين بالعملاء لتقديم تجربة طهي استثنائية في منزلك',
                'description_en' => 'A platform connecting professional chefs with customers for an exceptional cooking experience at your home',
                'display_order' => 0,
                'is_active' => true,
                'additional_data' => [
                    'slides' => [
                        [
                            'title_ar' => 'اكتشف أفضل الطهاة',
                            'title_en' => 'Discover the Best Chefs',
                            'description_ar' => 'تصفح مجموعة واسعة من الطهاة المحترفين واختر الأنسب لمناسبتك',
                            'description_en' => 'Browse a wide selection of professional chefs and choose the perfect one for your occasion',
                            'image' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?w=1920&q=80'
                        ],
                        [
                            'title_ar' => 'احجز بسهولة وأمان',
                            'title_en' => 'Book Easily and Securely',
                            'description_ar' => 'نظام حجز سهل وآمن مع خيارات دفع متعددة',
                            'description_en' => 'Easy and secure booking system with multiple payment options',
                            'image' => 'https://images.unsplash.com/photo-1466637574441-749b8f19452f?w=1920&q=80'
                        ],
                        [
                            'title_ar' => 'تجربة طهي فريدة',
                            'title_en' => 'Unique Cooking Experience',
                            'description_ar' => 'استمتع بوجبات شهية محضرة بحب واحترافية في منزلك',
                            'description_en' => 'Enjoy delicious meals prepared with love and professionalism at your home',
                            'image' => 'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=1920&q=80'
                        ],
                    ],
                ],
            ],

            // Features Section
            [
                'section_key' => 'features',
                'title_ar' => 'لماذا مون شيف؟',
                'title_en' => 'Why Moon Chef?',
                'description_ar' => 'نقدم لك تجربة فريدة تجمع بين الجودة والراحة والاحترافية',
                'description_en' => 'We offer you a unique experience combining quality, comfort, and professionalism',
                'display_order' => 1,
                'is_active' => true,
                'additional_data' => [
                    'features' => [
                        [
                            'icon' => 'chef-hat',
                            'title_ar' => 'طهاة محترفون',
                            'title_en' => 'Professional Chefs',
                            'description_ar' => 'جميع الطهاة معتمدون وذوو خبرة عالية',
                            'description_en' => 'All chefs are certified and highly experienced'
                        ],
                        [
                            'icon' => 'calendar',
                            'title_ar' => 'حجز مرن',
                            'title_en' => 'Flexible Booking',
                            'description_ar' => 'احجز في الوقت الذي يناسبك',
                            'description_en' => 'Book at a time that suits you'
                        ],
                        [
                            'icon' => 'star',
                            'title_ar' => 'تقييمات موثوقة',
                            'title_en' => 'Trusted Reviews',
                            'description_ar' => 'اقرأ تقييمات العملاء السابقين',
                            'description_en' => 'Read reviews from previous customers'
                        ],
                        [
                            'icon' => 'shield',
                            'title_ar' => 'دفع آمن',
                            'title_en' => 'Secure Payment',
                            'description_ar' => 'نظام دفع آمن ومشفر',
                            'description_en' => 'Secure and encrypted payment system'
                        ],
                        [
                            'icon' => 'utensils',
                            'title_ar' => 'تنوع في القوائم',
                            'title_en' => 'Diverse Menus',
                            'description_ar' => 'مطابخ عالمية ومحلية متنوعة',
                            'description_en' => 'Diverse international and local cuisines'
                        ],
                        [
                            'icon' => 'support',
                            'title_ar' => 'دعم متواصل',
                            'title_en' => '24/7 Support',
                            'description_ar' => 'فريق دعم متاح دائماً لمساعدتك',
                            'description_en' => 'Support team always available to help you'
                        ],
                    ],
                ],
            ],

            // How It Works Section
            [
                'section_key' => 'how_it_works',
                'title_ar' => 'كيف يعمل مون شيف؟',
                'title_en' => 'How Does Moon Chef Work?',
                'description_ar' => 'احجز طاهيك في 3 خطوات بسيطة',
                'description_en' => 'Book your chef in 3 simple steps',
                'display_order' => 2,
                'is_active' => true,
                'additional_data' => [
                    'steps' => [
                        [
                            'step' => 1,
                            'title_ar' => 'اختر الطاهي',
                            'title_en' => 'Choose Your Chef',
                            'description_ar' => 'تصفح قائمة الطهاة واختر الأنسب لك',
                            'description_en' => 'Browse the list of chefs and choose the best one for you',
                            'icon' => 'Search'
                        ],
                        [
                            'step' => 2,
                            'title_ar' => 'حدد التفاصيل',
                            'title_en' => 'Set the Details',
                            'description_ar' => 'اختر التاريخ والوقت ونوع الخدمة',
                            'description_en' => 'Choose the date, time, and type of service',
                            'icon' => 'CalendarCheck'
                        ],
                        [
                            'step' => 3,
                            'title_ar' => 'استمتع بالتجربة',
                            'title_en' => 'Enjoy the Experience',
                            'description_ar' => 'استرخ واستمتع بوجبة شهية في منزلك',
                            'description_en' => 'Relax and enjoy a delicious meal at your home',
                            'icon' => 'Smile'
                        ],
                    ],
                ],
            ],

            // Top Chefs Section
            [
                'section_key' => 'top_chefs',
                'title_ar' => 'أفضل الطهاة',
                'title_en' => 'Top Chefs',
                'description_ar' => 'تعرف على أفضل الطهاة المحترفين على منصتنا',
                'description_en' => 'Meet the best professional chefs on our platform',
                'display_order' => 3,
                'is_active' => true,
                'additional_data' => [
                    'note_ar' => 'سيتم عرض الطهاة الأعلى تقييماً تلقائياً',
                    'note_en' => 'Top-rated chefs will be displayed automatically',
                ],
            ],

            // Categories Section
            [
                'section_key' => 'categories',
                'title_ar' => 'تصنيفات المطابخ',
                'title_en' => 'Cuisine Categories',
                'description_ar' => 'اكتشف مجموعة متنوعة من المطابخ العالمية والمحلية',
                'description_en' => 'Discover a variety of international and local cuisines',
                'display_order' => 4,
                'is_active' => true,
                'additional_data' => [
                    'note_ar' => 'سيتم عرض التصنيفات تلقائياً من قاعدة البيانات',
                    'note_en' => 'Categories will be displayed automatically from the database',
                ],
            ],

            // Testimonials Section
            [
                'section_key' => 'testimonials',
                'title_ar' => 'آراء العملاء',
                'title_en' => 'Customer Reviews',
                'description_ar' => 'ماذا يقول عملاؤنا عن تجربتهم',
                'description_en' => 'What our customers say about their experience',
                'display_order' => 5,
                'is_active' => true,
                'additional_data' => [
                    'testimonials' => [
                        [
                            'name_ar' => 'أحمد محمد',
                            'name_en' => 'Ahmed Mohammed',
                            'rating' => 5,
                            'comment_ar' => 'تجربة رائعة! الطاهي كان محترف جداً والطعام كان لذيذ',
                            'comment_en' => 'Amazing experience! The chef was very professional and the food was delicious',
                            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150'
                        ],
                        [
                            'name_ar' => 'فاطمة علي',
                            'name_en' => 'Fatima Ali',
                            'rating' => 5,
                            'comment_ar' => 'خدمة ممتازة وسهولة في الحجز. أنصح الجميع بتجربة مون شيف',
                            'comment_en' => 'Excellent service and easy booking. I recommend everyone to try Moon Chef',
                            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150'
                        ],
                        [
                            'name_ar' => 'خالد سعيد',
                            'name_en' => 'Khaled Saeed',
                            'rating' => 5,
                            'comment_ar' => 'أفضل منصة لحجز الطهاة. الطعام كان رائع والخدمة احترافية',
                            'comment_en' => 'Best platform for booking chefs. The food was amazing and the service was professional',
                            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150'
                        ],
                    ],
                ],
            ],

            // About Us Section (detailed)
            [
                'section_key' => 'about_us',
                'title_ar' => 'من نحن',
                'title_en' => 'About Us',
                'description_ar' => 'مون شيف هي منصة يمنية رائدة تهدف إلى تحويل تجربة الطعام في المنازل من خلال ربط الطهاة المحترفين بالعملاء الباحثين عن تجربة طهي فريدة واحترافية',
                'description_en' => 'Moon Chef is a leading Yemeni platform aimed at transforming the home dining experience by connecting professional chefs with customers seeking a unique and professional cooking experience',
                'display_order' => 11,
                'is_active' => true,
                'additional_data' => [
                    'story_ar' => 'بدأت فكرة مون شيف من رغبتنا في جعل تجربة الطعام الفاخر متاحة للجميع في منازلهم. نؤمن بأن كل مناسبة تستحق طعاماً استثنائياً، ولهذا قمنا بإنشاء منصة تجمع أفضل الطهاة المحترفين في مكان واحد.',
                    'story_en' => 'The idea of Moon Chef started from our desire to make fine dining accessible to everyone in their homes. We believe every occasion deserves exceptional food, which is why we created a platform that brings together the best professional chefs in one place.',
                    'values' => [
                        [
                            'title_ar' => 'الجودة',
                            'title_en' => 'Quality',
                            'description_ar' => 'نلتزم بأعلى معايير الجودة في اختيار الطهاة والمكونات',
                            'description_en' => 'We commit to the highest quality standards in selecting chefs and ingredients'
                        ],
                        [
                            'title_ar' => 'الاحترافية',
                            'title_en' => 'Professionalism',
                            'description_ar' => 'فريق من الطهاة المحترفين المعتمدين وذوي الخبرة',
                            'description_en' => 'A team of certified and experienced professional chefs'
                        ],
                        [
                            'title_ar' => 'الثقة',
                            'title_en' => 'Trust',
                            'description_ar' => 'نبني علاقات طويلة الأمد مع عملائنا من خلال الشفافية والمصداقية',
                            'description_en' => 'We build long-term relationships with our customers through transparency and credibility'
                        ],
                        [
                            'title_ar' => 'الابتكار',
                            'title_en' => 'Innovation',
                            'description_ar' => 'نسعى دائماً لتطوير خدماتنا وتقديم تجارب جديدة',
                            'description_en' => 'We always strive to develop our services and provide new experiences'
                        ],
                    ],
                ],
            ],

            // Vision & Mission Section
            [
                'section_key' => 'vision_mission',
                'title_ar' => 'رؤيتنا ورسالتنا',
                'title_en' => 'Our Vision & Mission',
                'description_ar' => 'نسعى لتحقيق رؤية طموحة ورسالة واضحة في خدمة عملائنا',
                'description_en' => 'We strive to achieve an ambitious vision and clear mission in serving our customers',
                'display_order' => 12,
                'is_active' => true,
                'additional_data' => [
                    'vision' => [
                        'title_ar' => 'رؤيتنا',
                        'title_en' => 'Our Vision',
                        'description_ar' => 'أن نكون المنصة الأولى والأكثر موثوقية في المنطقة لربط الطهاة المحترفين بالعملاء، وأن نساهم في تطوير صناعة الطهي وتقديم تجارب طعام استثنائية في كل منزل',
                        'description_en' => 'To be the first and most trusted platform in the region connecting professional chefs with customers, and to contribute to developing the culinary industry and providing exceptional dining experiences in every home',
                        'icon' => 'eye'
                    ],
                    'mission' => [
                        'title_ar' => 'رسالتنا',
                        'title_en' => 'Our Mission',
                        'description_ar' => 'توفير تجربة طهي فريدة واحترافية في منازل عملائنا من خلال ربطهم بأفضل الطهاة المحترفين، مع ضمان أعلى معايير الجودة والنظافة والاحترافية في كل خدمة نقدمها',
                        'description_en' => 'Providing a unique and professional cooking experience in our customers\' homes by connecting them with the best professional chefs, while ensuring the highest standards of quality, cleanliness, and professionalism in every service we provide',
                        'icon' => 'target'
                    ],
                    'goals' => [
                        [
                            'title_ar' => 'التوسع الجغرافي',
                            'title_en' => 'Geographic Expansion',
                            'description_ar' => 'الوصول إلى جميع المحافظات اليمنية',
                            'description_en' => 'Reaching all Yemeni governorates'
                        ],
                        [
                            'title_ar' => 'تطوير الطهاة',
                            'title_en' => 'Chef Development',
                            'description_ar' => 'تدريب وتطوير مهارات الطهاة المحليين',
                            'description_en' => 'Training and developing local chefs\' skills'
                        ],
                        [
                            'title_ar' => 'رضا العملاء',
                            'title_en' => 'Customer Satisfaction',
                            'description_ar' => 'تحقيق أعلى معدلات رضا العملاء',
                            'description_en' => 'Achieving the highest customer satisfaction rates'
                        ],
                    ],
                ],
            ],

            // Why Us Section
            [
                'section_key' => 'why_us',
                'title_ar' => 'لماذا تختار مون شيف؟',
                'title_en' => 'Why Choose Moon Chef?',
                'description_ar' => 'نقدم لك مزايا فريدة تجعلنا الخيار الأمثل لتجربة طهي استثنائية',
                'description_en' => 'We offer you unique advantages that make us the best choice for an exceptional cooking experience',
                'display_order' => 13,
                'is_active' => true,
                'additional_data' => [
                    'reasons' => [
                        [
                            'icon' => 'Certificate',
                            'title_ar' => 'طهاة معتمدون',
                            'title_en' => 'Certified Chefs',
                            'description_ar' => 'جميع طهاتنا حاصلون على شهادات معتمدة ولديهم سنوات من الخبرة في مجال الطهي الاحترافي',
                            'description_en' => 'All our chefs are certified and have years of experience in professional cooking'
                        ],
                        [
                            'icon' => 'ShieldCheck',
                            'title_ar' => 'معايير صحية عالية',
                            'title_en' => 'High Health Standards',
                            'description_ar' => 'نلتزم بأعلى معايير النظافة والسلامة الصحية في جميع خدماتنا',
                            'description_en' => 'We adhere to the highest standards of cleanliness and health safety in all our services'
                        ],
                        [
                            'icon' => 'Clock',
                            'title_ar' => 'مرونة في المواعيد',
                            'title_en' => 'Flexible Scheduling',
                            'description_ar' => 'احجز في الوقت الذي يناسبك، سواء كان صباحاً أو مساءً أو في عطلة نهاية الأسبوع',
                            'description_en' => 'Book at a time that suits you, whether morning, evening, or weekend'
                        ],
                        [
                            'icon' => 'DollarSign',
                            'title_ar' => 'أسعار تنافسية',
                            'title_en' => 'Competitive Prices',
                            'description_ar' => 'نقدم أفضل الأسعار مقابل خدمات عالية الجودة',
                            'description_en' => 'We offer the best prices for high-quality services'
                        ],
                        [
                            'icon' => 'Users',
                            'title_ar' => 'خدمة عملاء متميزة',
                            'title_en' => 'Excellent Customer Service',
                            'description_ar' => 'فريق دعم متاح على مدار الساعة للإجابة على استفساراتك',
                            'description_en' => 'Support team available 24/7 to answer your inquiries'
                        ],
                        [
                            'icon' => 'Heart',
                            'title_ar' => 'تجربة شخصية',
                            'title_en' => 'Personalized Experience',
                            'description_ar' => 'نصمم تجربة طهي خاصة تناسب ذوقك واحتياجاتك',
                            'description_en' => 'We design a special cooking experience that suits your taste and needs'
                        ],
                    ],
                    'stats' => [
                        [
                            'number' => '500+',
                            'label_ar' => 'طاهي محترف',
                            'label_en' => 'Professional Chef'
                        ],
                        [
                            'number' => '10,000+',
                            'label_ar' => 'عميل راضٍ',
                            'label_en' => 'Satisfied Customer'
                        ],
                        [
                            'number' => '15,000+',
                            'label_ar' => 'حجز ناجح',
                            'label_en' => 'Successful Booking'
                        ],
                        [
                            'number' => '4.9/5',
                            'label_ar' => 'تقييم العملاء',
                            'label_en' => 'Customer Rating'
                        ],
                    ],
                ],
            ],

            // Partners Section
            [
                'section_key' => 'partners',
                'title_ar' => 'شركاؤنا',
                'title_en' => 'Our Partners',
                'description_ar' => 'نفخر بشراكتنا مع أفضل المؤسسات والعلامات التجارية',
                'description_en' => 'We are proud of our partnership with the best institutions and brands',
                'display_order' => 14,
                'is_active' => true,
                'additional_data' => [
                    'partners' => [
                        [
                            'name_ar' => 'مطاعم الذواقة',
                            'name_en' => 'Gourmet Restaurants',
                            'logo' => 'https://via.placeholder.com/200x100?text=Partner+1',
                            'description_ar' => 'شريك في توفير أفضل المكونات',
                            'description_en' => 'Partner in providing the best ingredients'
                        ],
                        [
                            'name_ar' => 'أكاديمية الطهي الدولية',
                            'name_en' => 'International Culinary Academy',
                            'logo' => 'https://via.placeholder.com/200x100?text=Partner+2',
                            'description_ar' => 'شريك في تدريب وتطوير الطهاة',
                            'description_en' => 'Partner in training and developing chefs'
                        ],
                        [
                            'name_ar' => 'سوق المزارعين المحليين',
                            'name_en' => 'Local Farmers Market',
                            'logo' => 'https://via.placeholder.com/200x100?text=Partner+3',
                            'description_ar' => 'شريك في توفير المنتجات الطازجة',
                            'description_en' => 'Partner in providing fresh products'
                        ],
                        [
                            'name_ar' => 'معهد الضيافة والسياحة',
                            'name_en' => 'Hospitality & Tourism Institute',
                            'logo' => 'https://via.placeholder.com/200x100?text=Partner+4',
                            'description_ar' => 'شريك في التدريب المهني',
                            'description_en' => 'Partner in professional training'
                        ],
                        [
                            'name_ar' => 'شركة المعدات المطبخية',
                            'name_en' => 'Kitchen Equipment Company',
                            'logo' => 'https://via.placeholder.com/200x100?text=Partner+5',
                            'description_ar' => 'شريك في توفير أفضل المعدات',
                            'description_en' => 'Partner in providing the best equipment'
                        ],
                        [
                            'name_ar' => 'جمعية الطهاة اليمنيين',
                            'name_en' => 'Yemeni Chefs Association',
                            'logo' => 'https://via.placeholder.com/200x100?text=Partner+6',
                            'description_ar' => 'شريك في دعم الطهاة المحليين',
                            'description_en' => 'Partner in supporting local chefs'
                        ],
                    ],
                    'partnership_benefits' => [
                        [
                            'title_ar' => 'جودة مضمونة',
                            'title_en' => 'Guaranteed Quality',
                            'description_ar' => 'شراكات مع أفضل الموردين لضمان جودة المكونات',
                            'description_en' => 'Partnerships with the best suppliers to ensure ingredient quality'
                        ],
                        [
                            'title_ar' => 'تدريب مستمر',
                            'title_en' => 'Continuous Training',
                            'description_ar' => 'برامج تدريب مستمرة لتطوير مهارات الطهاة',
                            'description_en' => 'Continuous training programs to develop chefs\' skills'
                        ],
                        [
                            'title_ar' => 'دعم محلي',
                            'title_en' => 'Local Support',
                            'description_ar' => 'دعم المنتجات والطهاة المحليين',
                            'description_en' => 'Supporting local products and chefs'
                        ],
                    ],
                ],
            ],

            // Contact Section
            [
                'section_key' => 'contact',
                'title_ar' => 'تواصل معنا',
                'title_en' => 'Contact Us',
                'description_ar' => 'نحن هنا لمساعدتك في أي وقت',
                'description_en' => 'We are here to help you anytime',
                'display_order' => 15,
                'is_active' => true,
                'additional_data' => [
                    'email' => 'info@moonchef.com',
                    'phone' => '+967 777 777 777',
                    'address_ar' => 'صنعاء، اليمن',
                    'address_en' => 'Sana\'a, Yemen',
                    'working_hours_ar' => 'السبت - الخميس: 9:00 ص - 9:00 م',
                    'working_hours_en' => 'Saturday - Thursday: 9:00 AM - 9:00 PM',
                    'social_links' => [
                        ['platform' => 'facebook', 'url' => 'https://facebook.com/moonchef'],
                        ['platform' => 'twitter', 'url' => 'https://twitter.com/moonchef'],
                        ['platform' => 'instagram', 'url' => 'https://instagram.com/moonchef'],
                        ['platform' => 'whatsapp', 'url' => 'https://wa.me/967777777777'],
                    ],
                ],
            ],
        ];

        foreach ($sections as $section) {
            LandingPageSection::create($section);
        }
    }
}
