<?php

namespace Domain\BusinessBundle\Model;

class SubscriptionModel
{
    public static function getSuperVmSubscriptions()
    {
        $categories = array (
            'alumar-doors-and-window-corp' =>
                array (
                    'name' => 'Alumar Doors & Window Corp.',
                    'slug' => 'alumar-doors-and-window-corp',
                    'date' => '28.02.2017',
                ),
            'cuatro-lunas-restaurant-and-martini-bar' =>
                array (
                    'name' => 'Cuatro Lunas Restaurant & Martini Bar',
                    'slug' => 'cuatro-lunas-restaurant-and-martini-bar',
                    'date' => '04.03.2016',
                ),
            'farmacia-el-divino-nino' =>
                array (
                    'name' => 'Farmacia El Divino Niño',
                    'slug' => 'farmacia-el-divino-nino',
                    'date' => '08.03.2016',
                ),
            'pro-kitchens-and-more' =>
                array (
                    'name' => 'Pro Kitchen\'s and More',
                    'slug' => 'pro-kitchens-and-more',
                    'date' => '08.03.2016',
                ),
            'social-designs' =>
                array (
                    'name' => 'Social Designs',
                    'slug' => 'social-designs',
                    'date' => '09.03.2016',
                ),
            'eric-r-vera-agente-de-seguros' =>
                array (
                    'name' => 'Eric R. Vera-Agente de Seguros',
                    'slug' => 'eric-r-vera-agente-de-seguros',
                    'date' => '11.03.2016',
                ),
            'eric-r-vera-agente-de-seguros-1' =>
                array (
                    'name' => 'Eric R. Vera-Agente de Seguros',
                    'slug' => 'eric-r-vera-agente-de-seguros-1',
                    'date' => '11.03.2016',
                ),
            'eco-real-estate-1' =>
                array (
                    'name' => 'Eco Real Estate',
                    'slug' => 'eco-real-estate-1',
                    'date' => '16.03.2016',
                ),
            'rainbow-global-solutions' =>
                array (
                    'name' => 'Rainbow Global Solutions',
                    'slug' => 'rainbow-global-solutions',
                    'date' => '17.03.2016',
                ),
            'gruas-pagan-inc' =>
                array (
                    'name' => 'Grúas Pagán Inc.',
                    'slug' => 'gruas-pagan-inc',
                    'date' => '13.03.2017',
                ),
            'anthoinette-beauty-salon-and-spa' =>
                array (
                    'name' => 'Anthoinette Beauty Salon & Spa',
                    'slug' => 'anthoinette-beauty-salon-and-spa',
                    'date' => '31.03.2016',
                ),
            'torres-towing-2' =>
                array (
                    'name' => 'Torres Towing',
                    'slug' => 'torres-towing-2',
                    'date' => '31.03.2016',
                ),
            'river-china-restaurant' =>
                array (
                    'name' => 'River China Restaurant',
                    'slug' => 'river-china-restaurant',
                    'date' => '31.03.2016',
                ),
            'farmacia-gabriela-2' =>
                array (
                    'name' => 'Farmacia Gabriela',
                    'slug' => 'farmacia-gabriela-2',
                    'date' => '06.04.2016',
                ),
            'farmacia-gabriela-3' =>
                array (
                    'name' => 'Farmacia Gabriela',
                    'slug' => 'farmacia-gabriela-3',
                    'date' => '06.04.2016',
                ),
            'farmacia-san-carlos-3' =>
                array (
                    'name' => 'Farmacia San Carlos',
                    'slug' => 'farmacia-san-carlos-3',
                    'date' => '30.03.2017',
                ),
            'vacunas-med' =>
                array (
                    'name' => 'Vacunas Med',
                    'slug' => 'vacunas-med',
                    'date' => '08.04.2016',
                ),
            'nalditos-bus-line-inc' =>
                array (
                    'name' => 'Naldito\'s Bus Line Inc.',
                    'slug' => 'nalditos-bus-line-inc',
                    'date' => '11.04.2016',
                ),
            'experts-communication-5' =>
                array (
                    'name' => 'Expert Communication',
                    'slug' => 'experts-communication-5',
                    'date' => '11.04.2016',
                ),
            'experts-communication-4' =>
                array (
                    'name' => 'Expert Communication',
                    'slug' => 'experts-communication-4',
                    'date' => '11.04.2016',
                ),
            'experts-communication-1' =>
                array (
                    'name' => 'Expert Communication',
                    'slug' => 'experts-communication-1',
                    'date' => '11.04.2016',
                ),
            'experts-communication' =>
                array (
                    'name' => 'Expert Communication',
                    'slug' => 'experts-communication',
                    'date' => '11.04.2016',
                ),
            'experts-communication-2' =>
                array (
                    'name' => 'Expert Communication',
                    'slug' => 'experts-communication-2',
                    'date' => '11.04.2016',
                ),
            'experts-communication-3' =>
                array (
                    'name' => 'Expert Communication',
                    'slug' => 'experts-communication-3',
                    'date' => '11.04.2016',
                ),
            'hogar-mi-pequeno-paraiso' =>
                array (
                    'name' => 'Hogar Mi Pequeño Paraíso',
                    'slug' => 'hogar-mi-pequeno-paraiso',
                    'date' => '31.03.2017',
                ),
            'rotulos-santiago' =>
                array (
                    'name' => 'Rótulos Santiago',
                    'slug' => 'rotulos-santiago',
                    'date' => '14.04.2016',
                ),
            'contrabando-restaurante' =>
                array (
                    'name' => 'Contrabando Restaurante',
                    'slug' => 'contrabando-restaurante',
                    'date' => '17.04.2016',
                ),
            'rivera-velez-and-santiago-llc' =>
                array (
                    'name' => 'Rivera-Velez & Santiago LLC.',
                    'slug' => 'rivera-velez-and-santiago-llc',
                    'date' => '17.04.2016',
                ),
            'nuestra-farmacia-1' =>
                array (
                    'name' => 'Nuestra Farmacia',
                    'slug' => 'nuestra-farmacia-1',
                    'date' => '19.04.2016',
                ),
            'aldalers-exterminating' =>
                array (
                    'name' => 'Aldalers Exterminating',
                    'slug' => 'aldalers-exterminating',
                    'date' => '19.04.2016',
                ),
            'centro-maderas-anasco-y-camuy' =>
                array (
                    'name' => 'Centro Maderas Añasco y Camuy',
                    'slug' => 'centro-maderas-anasco-y-camuy',
                    'date' => '30.03.2017',
                ),
            'montehiedra-mri-and-ct-center' =>
                array (
                    'name' => 'Montehiedra MRI & CT Center',
                    'slug' => 'montehiedra-mri-and-ct-center',
                    'date' => '28.04.2016',
                ),
            'barceloneta-bbq' =>
                array (
                    'name' => 'Barceloneta BBQ',
                    'slug' => 'barceloneta-bbq',
                    'date' => '02.05.2016',
                ),
            'unioffice-express' =>
                array (
                    'name' => 'Unioffice Express',
                    'slug' => 'unioffice-express',
                    'date' => '03.05.2016',
                ),
            'unitec-de-pr' =>
                array (
                    'name' => 'Unitec de PR',
                    'slug' => 'unitec-de-pr',
                    'date' => '03.05.2016',
                ),
            'love-it-shoe-boutique-1' =>
                array (
                    'name' => 'Love It Shoe Boutique',
                    'slug' => 'love-it-shoe-boutique-1',
                    'date' => '04.05.2016',
                ),
            'golden-paradise-2' =>
                array (
                    'name' => 'Golden Paradise',
                    'slug' => 'golden-paradise-2',
                    'date' => '04.05.2016',
                ),
            'olajas-bar-and-grill' =>
                array (
                    'name' => 'Olajas Bar & Grill',
                    'slug' => 'olajas-bar-and-grill',
                    'date' => '07.05.2016',
                ),
            'pitos-pizza' =>
                array (
                    'name' => 'Pitos Pizza',
                    'slug' => 'pitos-pizza',
                    'date' => '12.05.2016',
                ),
            'hogar-dando-vida' =>
                array (
                    'name' => 'Hogar Dando Vida',
                    'slug' => 'hogar-dando-vida',
                    'date' => '07.04.2017',
                ),
            'farmacia-chaves' =>
                array (
                    'name' => 'Farmacia Chaves',
                    'slug' => 'farmacia-chaves',
                    'date' => '18.05.2016',
                ),
            'central-towing-and-transport-inc' =>
                array (
                    'name' => 'Central Towing & Transport',
                    'slug' => 'central-towing-and-transport-inc',
                    'date' => '19.05.2016',
                ),
            'alberto-middelhof-de-leon-md' =>
                array (
                    'name' => 'Alberto Middelhof de León MD',
                    'slug' => 'alberto-middelhof-de-leon-md',
                    'date' => '19.05.2016',
                ),
            'air-master-windows-and-doors-2' =>
                array (
                    'name' => 'Air Master Windows and Doors',
                    'slug' => 'air-master-windows-and-doors-2',
                    'date' => '19.05.2016',
                ),
            'campo-rico-animal-clinic-1' =>
                array (
                    'name' => 'Campo Rico Animal Clinic',
                    'slug' => 'campo-rico-animal-clinic-1',
                    'date' => '20.05.2016',
                ),
            'legal-express-9' =>
                array (
                    'name' => 'Legal Express',
                    'slug' => 'legal-express-9',
                    'date' => '24.05.2016',
                ),
            'la-maison-suisse' =>
                array (
                    'name' => 'La Maison Suisse',
                    'slug' => 'la-maison-suisse',
                    'date' => '24.05.2016',
                ),
            'la-maison-suisse-1' =>
                array (
                    'name' => 'La Maison Suisse',
                    'slug' => 'la-maison-suisse-1',
                    'date' => '24.05.2016',
                ),
            'abeja-towing-service' =>
                array (
                    'name' => 'Abeja Towing Service',
                    'slug' => 'abeja-towing-service',
                    'date' => '25.05.2016',
                ),
            'rapid-oil-change-total' =>
                array (
                    'name' => 'Rapid Oil Change Total',
                    'slug' => 'rapid-oil-change-total',
                    'date' => '25.05.2016',
                ),
            'adore-boutique-3' =>
                array (
                    'name' => 'Adore boutique',
                    'slug' => 'adore-boutique-3',
                    'date' => '26.05.2016',
                ),
            'fds-aluminum' =>
                array (
                    'name' => 'FDS Aluminum',
                    'slug' => 'fds-aluminum',
                    'date' => '26.05.2016',
                ),
            'friends-cafe-litoral' =>
                array (
                    'name' => 'Friend\'s Café Litoral',
                    'slug' => 'friends-cafe-litoral',
                    'date' => '27.05.2016',
                ),
            'super-borinquen-tire' =>
                array (
                    'name' => 'Super Borinquen Tire',
                    'slug' => 'super-borinquen-tire',
                    'date' => '31.05.2016',
                ),
            'shas-restaurant-1' =>
                array (
                    'name' => 'Sha\'s Restaurant',
                    'slug' => 'shas-restaurant-1',
                    'date' => '02.06.2016',
                ),
            'morales-and-perez-accounting-office' =>
                array (
                    'name' => 'Morales & Pérez Accounting Office',
                    'slug' => 'morales-and-perez-accounting-office',
                    'date' => '02.06.2016',
                ),
            'novelty-flower' =>
                array (
                    'name' => 'Novelty Flower',
                    'slug' => 'novelty-flower',
                    'date' => '02.06.2016',
                ),
            'colegio-giseland' =>
                array (
                    'name' => 'Colegio Giseland',
                    'slug' => 'colegio-giseland',
                    'date' => '03.06.2016',
                ),
            'merendola' =>
                array (
                    'name' => 'Merendola',
                    'slug' => 'merendola',
                    'date' => '05.06.2016',
                ),
            'apolo-puma-servicentro' =>
                array (
                    'name' => 'Apolo Puma Servicentro',
                    'slug' => 'apolo-puma-servicentro',
                    'date' => '07.06.2016',
                ),
            'dra-agnes-charles-torres' =>
                array (
                    'name' => 'Charles Torres Agnes L. Dra.',
                    'slug' => 'dra-agnes-charles-torres',
                    'date' => '13.06.2016',
                ),
            'caribbean-radiators' =>
                array (
                    'name' => 'Caribbean Radiators',
                    'slug' => 'caribbean-radiators',
                    'date' => '13.06.2016',
                ),
            'drapery-creations-of-pirette' =>
                array (
                    'name' => 'Drapery Creations of Pirette',
                    'slug' => 'drapery-creations-of-pirette',
                    'date' => '13.06.2016',
                ),
            'medina-edmond-dr-cirujano-podiatra' =>
                array (
                    'name' => 'Medina Edmond Dr. Cirujano Podiatra',
                    'slug' => 'medina-edmond-dr-cirujano-podiatra',
                    'date' => '14.06.2016',
                ),
            'clinica-veterinaria-central' =>
                array (
                    'name' => 'Clínica Veterinaria Central',
                    'slug' => 'clinica-veterinaria-central',
                    'date' => '15.06.2016',
                ),
            'je-advertising' =>
                array (
                    'name' => 'JE Advertising',
                    'slug' => 'je-advertising',
                    'date' => '15.06.2016',
                ),
            'laboratorio-clinico-irizarry-guasch-ponce-1' =>
                array (
                    'name' => 'Laboratorio Clínico Irizarry Guasch',
                    'slug' => 'laboratorio-clinico-irizarry-guasch-ponce-1',
                    'date' => '17.06.2016',
                ),
            'luminaries' =>
                array (
                    'name' => 'Luminaries',
                    'slug' => 'luminaries',
                    'date' => '17.06.2016',
                ),
            'laboratorio-clinico-costa-caribe-1' =>
                array (
                    'name' => 'Laboratorio Clínico Costa Caribe',
                    'slug' => 'laboratorio-clinico-costa-caribe-1',
                    'date' => '17.06.2016',
                ),
            'copy-jet-impresion-digital-de-alta-definicion' =>
                array (
                    'name' => 'Copy Jet "Impresión Digital de Alta Definición"',
                    'slug' => 'copy-jet-impresion-digital-de-alta-definicion',
                    'date' => '17.06.2016',
                ),
            'albert-auto-air-and-radiadores' =>
                array (
                    'name' => 'Albert Auto Air & Radiadores',
                    'slug' => 'albert-auto-air-and-radiadores',
                    'date' => '22.06.2016',
                ),
            'deluxe-auto-collision-1' =>
                array (
                    'name' => 'Deluxe Auto Collision',
                    'slug' => 'deluxe-auto-collision-1',
                    'date' => '23.06.2016',
                ),
            'study-and-learn-with-me' =>
                array (
                    'name' => 'Study and Learn With Me',
                    'slug' => 'study-and-learn-with-me',
                    'date' => '24.06.2016',
                ),
            'pan-american-latino-express-inc-4' =>
                array (
                    'name' => 'Pan American Latino Express Inc.',
                    'slug' => 'pan-american-latino-express-inc-4',
                    'date' => '24.06.2016',
                ),
            'colegio-jardin-de-la-merced' =>
                array (
                    'name' => 'Colegio Jardín de la Merced',
                    'slug' => 'colegio-jardin-de-la-merced',
                    'date' => '27.06.2016',
                ),
            'la-fe-funeral-memories' =>
                array (
                    'name' => 'La Fe Funeral Memories',
                    'slug' => 'la-fe-funeral-memories',
                    'date' => '28.06.2016',
                ),
            'metropolitan-animal-clinic' =>
                array (
                    'name' => 'Metropolitan Animal Clinic',
                    'slug' => 'metropolitan-animal-clinic',
                    'date' => '28.06.2016',
                ),
            '4d-gelato-and-caffe' =>
                array (
                    'name' => '4D Gelato & Caffé',
                    'slug' => '4d-gelato-and-caffe',
                    'date' => '29.06.2016',
                ),
            'mi-a-salon-and-spa' =>
                array (
                    'name' => 'Mi\' A Salon & Spa',
                    'slug' => 'mi-a-salon-and-spa',
                    'date' => '30.06.2016',
                ),
            'vida-plus-medical-clinic-dr-joseph-s-campos' =>
                array (
                    'name' => 'Vida Plus Medical Clinic - Dr. Joseph S. Campos',
                    'slug' => 'vida-plus-medical-clinic-dr-joseph-s-campos',
                    'date' => '30.06.2016',
                ),
            'institucion-palacio-de-oro' =>
                array (
                    'name' => 'Institución Palacio de Oro',
                    'slug' => 'institucion-palacio-de-oro',
                    'date' => '30.06.2016',
                ),
            'xpress-graphics' =>
                array (
                    'name' => 'Xpress Graphics',
                    'slug' => 'xpress-graphics',
                    'date' => '30.06.2016',
                ),
            'hogar-angeles-para-la-3ra-edad' =>
                array (
                    'name' => 'Hogar ¡ngeles para la 3ra. Edad',
                    'slug' => 'hogar-angeles-para-la-3ra-edad',
                    'date' => '30.06.2016',
                ),
            'agros-de-borinquen' =>
                array (
                    'name' => 'Agros de Borinquen',
                    'slug' => 'agros-de-borinquen',
                    'date' => '30.06.2016',
                ),
            'centro-quiropractico-dr-jose-e-bobonis' =>
                array (
                    'name' => 'Centro Quiropráctico Dr. José E. Bobonis',
                    'slug' => 'centro-quiropractico-dr-jose-e-bobonis',
                    'date' => '05.07.2016',
                ),
            'farmacias-plaza' =>
                array (
                    'name' => 'Farmacias Plaza',
                    'slug' => 'farmacias-plaza',
                    'date' => '06.07.2016',
                ),
            'pr-floral-marketing-2' =>
                array (
                    'name' => 'P.R. Floral Marketing',
                    'slug' => 'pr-floral-marketing-2',
                    'date' => '07.07.2016',
                ),
            'pr-floral-marketing' =>
                array (
                    'name' => 'P.R. Floral Marketing',
                    'slug' => 'pr-floral-marketing',
                    'date' => '07.07.2016',
                ),
            'laboratorio-clinico-las-colinas' =>
                array (
                    'name' => 'Laboratorio Clínico Las Colinas',
                    'slug' => 'laboratorio-clinico-las-colinas',
                    'date' => '08.07.2016',
                ),
            'hospital-veterinario-de-puerto-rico' =>
                array (
                    'name' => 'Hospital Veterinario De Puerto Rico',
                    'slug' => 'hospital-veterinario-de-puerto-rico',
                    'date' => '11.07.2016',
                ),
            'dra-mayra-vera-vazquez' =>
                array (
                    'name' => 'Vera Vázquez Mayra Dra.',
                    'slug' => 'dra-mayra-vera-vazquez',
                    'date' => '12.07.2016',
                ),
            'la-nonna-pizza' =>
                array (
                    'name' => 'La Nonna Pizza',
                    'slug' => 'la-nonna-pizza',
                    'date' => '13.07.2016',
                ),
            'a-and-p-body-shop' =>
                array (
                    'name' => 'A & P Body Shop',
                    'slug' => 'a-and-p-body-shop',
                    'date' => '15.07.2016',
                ),
            'puerto-rico-auto-collision-1' =>
                array (
                    'name' => 'Puerto Rico Auto Collision',
                    'slug' => 'puerto-rico-auto-collision-1',
                    'date' => '18.07.2016',
                ),
            'lcda-wanda-v-hernandez-cardona' =>
                array (
                    'name' => 'Hernández Cardona Wanda V. Lcda.',
                    'slug' => 'lcda-wanda-v-hernandez-cardona',
                    'date' => '18.07.2016',
                ),
            'cetis-centro-especializado-en-terapias-e-integracion-sensorial' =>
                array (
                    'name' => 'CETIS/Centro Especializado en Terapias e Integración Sensorial',
                    'slug' => 'cetis-centro-especializado-en-terapias-e-integracion-sensorial',
                    'date' => '20.07.2016',
                ),
            'toyo-house' =>
                array (
                    'name' => 'Toyo House',
                    'slug' => 'toyo-house',
                    'date' => '21.07.2016',
                ),
            'coffee-time-corp' =>
                array (
                    'name' => 'Coffee Time Corp.',
                    'slug' => 'coffee-time-corp',
                    'date' => '22.07.2016',
                ),
            'floristeria-flores-lindas' =>
                array (
                    'name' => 'Floristería Flores Lindas',
                    'slug' => 'floristeria-flores-lindas',
                    'date' => '27.07.2016',
                ),
            'hogar-plenitud-dorada' =>
                array (
                    'name' => 'Hogar Plenitud Dorada',
                    'slug' => 'hogar-plenitud-dorada',
                    'date' => '29.07.2016',
                ),
            'mudanzas-torres' =>
                array (
                    'name' => 'Mudanzas Torres',
                    'slug' => 'mudanzas-torres',
                    'date' => '29.07.2016',
                ),
            'builder-supply-1' =>
                array (
                    'name' => 'Builder Supply',
                    'slug' => 'builder-supply-1',
                    'date' => '02.08.2016',
                ),
            'dra-ingrid-m-negron-valentin' =>
                array (
                    'name' => 'Negrón Valentín Ingrid M. Dra.',
                    'slug' => 'dra-ingrid-m-negron-valentin',
                    'date' => '02.08.2016',
                ),
            'old-san-juan-veterinary-center' =>
                array (
                    'name' => 'Old San Juan Veterinary Center',
                    'slug' => 'old-san-juan-veterinary-center',
                    'date' => '03.08.2016',
                ),
            'athenas-bilingual-academy' =>
                array (
                    'name' => 'Athena Bilingual Academy',
                    'slug' => 'athenas-bilingual-academy',
                    'date' => '03.08.2016',
                ),
            'santori-margarida-vicente-lcdo' =>
                array (
                    'name' => 'Santori Margarida Vicente Lcdo.',
                    'slug' => 'santori-margarida-vicente-lcdo',
                    'date' => '03.08.2016',
                ),
            'centro-de-reconstruccion-oral-e-implantes-1' =>
                array (
                    'name' => 'Centro de Reconstrucción Oral e Implantes',
                    'slug' => 'centro-de-reconstruccion-oral-e-implantes-1',
                    'date' => '04.08.2016',
                ),
            'narvaez-tire-center' =>
                array (
                    'name' => 'Narváez Tire Center',
                    'slug' => 'narvaez-tire-center',
                    'date' => '04.08.2016',
                ),
            'laboratorio-clinico-barbosa' =>
                array (
                    'name' => 'Laboratorio Clínico Barbosa',
                    'slug' => 'laboratorio-clinico-barbosa',
                    'date' => '04.08.2016',
                ),
            'i-love-you-lord-home-center' =>
                array (
                    'name' => 'I Love You Lord Home Center',
                    'slug' => 'i-love-you-lord-home-center',
                    'date' => '04.08.2016',
                ),
            'omart-print-and-signs' =>
                array (
                    'name' => 'Omart Print & Signs',
                    'slug' => 'omart-print-and-signs',
                    'date' => '04.08.2016',
                ),
            'buen-vecino-cafe' =>
                array (
                    'name' => 'Buen Vecino Café',
                    'slug' => 'buen-vecino-cafe',
                    'date' => '09.08.2016',
                ),
            'super-business-machines' =>
                array (
                    'name' => 'Super Business Machine',
                    'slug' => 'super-business-machines',
                    'date' => '10.08.2016',
                ),
            'laboratorio-clinico-sunny-hills' =>
                array (
                    'name' => 'Laboratorio Clínico Sunny Hills',
                    'slug' => 'laboratorio-clinico-sunny-hills',
                    'date' => '10.08.2016',
                ),
            'computer-transmission-service' =>
                array (
                    'name' => 'Computer Transmission Services',
                    'slug' => 'computer-transmission-service',
                    'date' => '11.08.2016',
                ),
            'msc-refrigeration-and-a-slash-c-contractor-multi-system-contractors-inc' =>
                array (
                    'name' => 'MSC Refrigeration & A/C Contractor (Multi System Contractors Inc.)',
                    'slug' => 'msc-refrigeration-and-a-slash-c-contractor-multi-system-contractors-inc',
                    'date' => '12.08.2016',
                ),
            'aurora-speedy-printing' =>
                array (
                    'name' => 'Aurora Speedy Printing',
                    'slug' => 'aurora-speedy-printing',
                    'date' => '12.08.2016',
                ),
            'centro-de-tutorias-para-universitarios' =>
                array (
                    'name' => 'Centro de Tutorías para Universitarios',
                    'slug' => 'centro-de-tutorias-para-universitarios',
                    'date' => '15.08.2016',
                ),
            'mannys-plumbing-2' =>
                array (
                    'name' => 'Manny\'s Plumbing',
                    'slug' => 'mannys-plumbing-2',
                    'date' => '16.08.2016',
                ),
            'todays-kids-montessori-1' =>
                array (
                    'name' => 'Today\'s Kids Montessori',
                    'slug' => 'todays-kids-montessori-1',
                    'date' => '16.08.2016',
                ),
            'montessori-garden-school' =>
                array (
                    'name' => 'Montessori Garden School',
                    'slug' => 'montessori-garden-school',
                    'date' => '17.08.2016',
                ),
            'centro-pre-escolar-y-estudios-complementarios-nim-inc' =>
                array (
                    'name' => 'Centro Pre-Escolar Y Estudios Complementarios NIM',
                    'slug' => 'centro-pre-escolar-y-estudios-complementarios-nim-inc',
                    'date' => '18.08.2016',
                ),
            'nieves-plumbing' =>
                array (
                    'name' => 'Nieves Plumbing',
                    'slug' => 'nieves-plumbing',
                    'date' => '18.08.2016',
                ),
            'dra-yahaira-moreno-y-dra-zhamarie-ortiz' =>
                array (
                    'name' => 'Moreno Yahaira Dra. y Ortiz Zhamarie Dra.',
                    'slug' => 'dra-yahaira-moreno-y-dra-zhamarie-ortiz',
                    'date' => '19.08.2016',
                ),
            'laboratorio-clinico-landron-v' =>
                array (
                    'name' => 'Laboratorio Clínico Landrón V',
                    'slug' => 'laboratorio-clinico-landron-v',
                    'date' => '23.08.2016',
                ),
            'el-castillo-magico-centro-de-cuidado-infantil-y-pre-escolar' =>
                array (
                    'name' => 'El Castillo Mágico Centro de Cuidado Infantil y Pre-Escolar',
                    'slug' => 'el-castillo-magico-centro-de-cuidado-infantil-y-pre-escolar',
                    'date' => '23.08.2016',
                ),
            'centro-de-rehabilitacion-y-electrodiagnostico-de-hato-rey' =>
                array (
                    'name' => 'Centro de Rehabilitación y Electrodiagnóstico de Hato Rey',
                    'slug' => 'centro-de-rehabilitacion-y-electrodiagnostico-de-hato-rey',
                    'date' => '24.08.2016',
                ),
            'any-parts-corp-5' =>
                array (
                    'name' => 'Any Parts Corp.',
                    'slug' => 'any-parts-corp-5',
                    'date' => '24.08.2016',
                ),
            'any-parts-corp-6' =>
                array (
                    'name' => 'Any Parts Corp.',
                    'slug' => 'any-parts-corp-6',
                    'date' => '24.08.2016',
                ),
            'any-parts-corp-7' =>
                array (
                    'name' => 'Any Parts Corp.',
                    'slug' => 'any-parts-corp-7',
                    'date' => '24.08.2016',
                ),
            'any-parts-corp' =>
                array (
                    'name' => 'Any Parts Corp.',
                    'slug' => 'any-parts-corp',
                    'date' => '24.08.2016',
                ),
            'any-parts-corp-4' =>
                array (
                    'name' => 'Any Parts Corp.',
                    'slug' => 'any-parts-corp-4',
                    'date' => '24.08.2016',
                ),
            'cied-clinica-terapeutica-pediatrica' =>
                array (
                    'name' => 'CIED Clinica Terapeutica',
                    'slug' => 'cied-clinica-terapeutica-pediatrica',
                    'date' => '25.08.2016',
                ),
            'hogar-vivencias-d-dot-a-z-dot-inc' =>
                array (
                    'name' => 'Hogar Vivencias D.A.Z.Inc.',
                    'slug' => 'hogar-vivencias-d-dot-a-z-dot-inc',
                    'date' => '25.08.2016',
                ),
            'abogados-seguro-social-choques-caidas-demandas-vizcarrondo-and-asoc-2' =>
                array (
                    'name' => 'Abogados Seguro Social, Choques, Caídas, Demandas Vizcarrondo & Asoc.',
                    'slug' => 'abogados-seguro-social-choques-caidas-demandas-vizcarrondo-and-asoc-2',
                    'date' => '30.08.2016',
                ),
            'abogados-seguro-social-choques-caidas-demandas-vizcarrondo-and-asoc' =>
                array (
                    'name' => 'Abogados Seguro Social, Choques, Caídas, Demandas Vizcarrondo & Asoc.',
                    'slug' => 'abogados-seguro-social-choques-caidas-demandas-vizcarrondo-and-asoc',
                    'date' => '30.08.2016',
                ),
            'abogados-seguro-social-choques-caidas-demandas-vizcarrondo-and-asoc-1' =>
                array (
                    'name' => 'Abogados Seguro Social, Choques, Caídas, Demandas Vizcarrondo & Asoc.',
                    'slug' => 'abogados-seguro-social-choques-caidas-demandas-vizcarrondo-and-asoc-1',
                    'date' => '30.08.2016',
                ),
            'pre-escolar-san-juan-evangelista' =>
                array (
                    'name' => 'Pre-Escolar San Juan Evangelista',
                    'slug' => 'pre-escolar-san-juan-evangelista',
                    'date' => '30.08.2016',
                ),
            'deresh-gary-m-dr-2' =>
                array (
                    'name' => 'Deresh Gary M. Dr.',
                    'slug' => 'deresh-gary-m-dr-2',
                    'date' => '30.08.2016',
                ),
            'deresh-gary-m-dr-1' =>
                array (
                    'name' => 'Deresh Gary M. Dr.',
                    'slug' => 'deresh-gary-m-dr-1',
                    'date' => '30.08.2016',
                ),
            'deresh-gary-m-dr-3' =>
                array (
                    'name' => 'Deresh Gary M. Dr.',
                    'slug' => 'deresh-gary-m-dr-3',
                    'date' => '30.08.2016',
                ),
            'vimar-therapy-group-1' =>
                array (
                    'name' => 'Vimar Therapy Group',
                    'slug' => 'vimar-therapy-group-1',
                    'date' => '30.08.2016',
                ),
            'vimar-therapy-group' =>
                array (
                    'name' => 'Vimar Therapy Group',
                    'slug' => 'vimar-therapy-group',
                    'date' => '30.08.2016',
                ),
            'maternelle' =>
                array (
                    'name' => 'Maternelle',
                    'slug' => 'maternelle',
                    'date' => '30.08.2016',
                ),
            'due-studio' =>
                array (
                    'name' => 'Due Studio',
                    'slug' => 'due-studio',
                    'date' => '31.08.2016',
                ),
            'automatic-equipment-inc' =>
                array (
                    'name' => 'Automatic Equipment Inc.',
                    'slug' => 'automatic-equipment-inc',
                    'date' => '31.08.2016',
                ),
            'sky-autoworks' =>
                array (
                    'name' => 'Sky Autoworks',
                    'slug' => 'sky-autoworks',
                    'date' => '31.08.2016',
                ),
            'taller-hermanos-ferran-1' =>
                array (
                    'name' => 'Taller Hermanos Ferrán',
                    'slug' => 'taller-hermanos-ferran-1',
                    'date' => '01.09.2016',
                ),
            'supernatural-tire-center' =>
                array (
                    'name' => 'Supernatural Tire Center',
                    'slug' => 'supernatural-tire-center',
                    'date' => '02.09.2016',
                ),
            'caribbean-auto-werks-and-body-shop' =>
                array (
                    'name' => 'Caribbean Auto Werks & Body Shop',
                    'slug' => 'caribbean-auto-werks-and-body-shop',
                    'date' => '06.09.2016',
                ),
            'carrasquillo-and-ramirez-abogados-notarios' =>
                array (
                    'name' => 'Carrasquillo & Ramírez Abogados Notarios',
                    'slug' => 'carrasquillo-and-ramirez-abogados-notarios',
                    'date' => '06.09.2016',
                ),
            'green-air-system-corp' =>
                array (
                    'name' => 'Green Air System Corp.',
                    'slug' => 'green-air-system-corp',
                    'date' => '07.09.2016',
                ),
            'laboratorio-clinico-torriver' =>
                array (
                    'name' => 'Laboratorio Clínico Torriver',
                    'slug' => 'laboratorio-clinico-torriver',
                    'date' => '08.09.2016',
                ),
            'megalum' =>
                array (
                    'name' => 'Megalum',
                    'slug' => 'megalum',
                    'date' => '08.09.2016',
                ),
            'megalum-1' =>
                array (
                    'name' => 'Megalum',
                    'slug' => 'megalum-1',
                    'date' => '08.09.2016',
                ),
            'megalum-4' =>
                array (
                    'name' => 'Megalum',
                    'slug' => 'megalum-4',
                    'date' => '08.09.2016',
                ),
            'megalum-3' =>
                array (
                    'name' => 'Megalum',
                    'slug' => 'megalum-3',
                    'date' => '08.09.2016',
                ),
            'megalum-2' =>
                array (
                    'name' => 'Megalum',
                    'slug' => 'megalum-2',
                    'date' => '08.09.2016',
                ),
            'nova-derm-1' =>
                array (
                    'name' => 'Nova Derm',
                    'slug' => 'nova-derm-1',
                    'date' => '09.09.2016',
                ),
            'nova-derm' =>
                array (
                    'name' => 'Nova Derm',
                    'slug' => 'nova-derm',
                    'date' => '09.09.2016',
                ),
            'fg-plumbing' =>
                array (
                    'name' => 'FG Plumbing',
                    'slug' => 'fg-plumbing',
                    'date' => '13.09.2016',
                ),
            'centro-quiropractico-dr-mariano-roman' =>
                array (
                    'name' => 'Centro Quiropráctico Dr. Mariano Román',
                    'slug' => 'centro-quiropractico-dr-mariano-roman',
                    'date' => '13.09.2016',
                ),
            'garaje-tolin' =>
                array (
                    'name' => 'Garaje Tolin',
                    'slug' => 'garaje-tolin',
                    'date' => '14.09.2016',
                ),
            'excellent-test-prep-1' =>
                array (
                    'name' => 'Excellent Test Prep',
                    'slug' => 'excellent-test-prep-1',
                    'date' => '15.09.2016',
                ),
            'excellence-test-prep' =>
                array (
                    'name' => 'Excellent Test Prep',
                    'slug' => 'excellence-test-prep',
                    'date' => '15.09.2016',
                ),
            'dr-jose-guerrero-de-leon' =>
                array (
                    'name' => 'Guerrero De León José Dr.',
                    'slug' => 'dr-jose-guerrero-de-leon',
                    'date' => '20.09.2016',
                ),
            'dominguez-vazquez-sergio-lcdo' =>
                array (
                    'name' => 'Domínguez Vázquez Sergio Lcdo.',
                    'slug' => 'dominguez-vazquez-sergio-lcdo',
                    'date' => '20.09.2016',
                ),
            'aor-building-supplies' =>
                array (
                    'name' => 'AOR Building Supplies',
                    'slug' => 'aor-building-supplies',
                    'date' => '22.09.2016',
                ),
            'faccio-pizza-2' =>
                array (
                    'name' => 'Faccio Pizza Caguas',
                    'slug' => 'faccio-pizza-2',
                    'date' => '27.09.2016',
                ),
            'garage-andalucia' =>
                array (
                    'name' => 'Garage Andalucía',
                    'slug' => 'garage-andalucia',
                    'date' => '27.09.2016',
                ),
            'centro-quiropractico-dr-elvin-siverio' =>
                array (
                    'name' => 'Centro Quiropráctico Dr. Elvin Siverio',
                    'slug' => 'centro-quiropractico-dr-elvin-siverio',
                    'date' => '27.09.2016',
                ),
            'triangle-toyota-dealer-de-nuevos-y-usados-san-juan' =>
                array (
                    'name' => 'Triangle Toyota de San Juan - Nuevos y Usados',
                    'slug' => 'triangle-toyota-dealer-de-nuevos-y-usados-san-juan',
                    'date' => '28.09.2016',
                ),
            'kute-gifts-and-flowers-1' =>
                array (
                    'name' => 'Kute Gifts & Flowers',
                    'slug' => 'kute-gifts-and-flowers-1',
                    'date' => '28.09.2016',
                ),
            'jpr-motorwerke' =>
                array (
                    'name' => 'JPR Motorwerke',
                    'slug' => 'jpr-motorwerke',
                    'date' => '28.09.2016',
                ),
            'colegio-de-la-vega' =>
                array (
                    'name' => 'Colegio De La Vega',
                    'slug' => 'colegio-de-la-vega',
                    'date' => '29.09.2016',
                ),
            'all-door-services-2' =>
                array (
                    'name' => 'All Door Services',
                    'slug' => 'all-door-services-2',
                    'date' => '29.09.2016',
                ),
            'amado-uniformes' =>
                array (
                    'name' => 'Amado Uniformes',
                    'slug' => 'amado-uniformes',
                    'date' => '29.09.2016',
                ),
            'farmacia-loudgar' =>
                array (
                    'name' => 'Farmacia Loudgar',
                    'slug' => 'farmacia-loudgar',
                    'date' => '29.09.2016',
                ),
            'guaynabo-refrigeration-services' =>
                array (
                    'name' => 'Guaynabo Refrigeration Services',
                    'slug' => 'guaynabo-refrigeration-services',
                    'date' => '30.09.2016',
                ),
            'puttanesca-italian-trattoria' =>
                array (
                    'name' => 'Puttanesca Italian Trattoria',
                    'slug' => 'puttanesca-italian-trattoria',
                    'date' => '03.10.2016',
                ),
            'american-exterminating-inc-7' =>
                array (
                    'name' => 'American Exterminating Inc.',
                    'slug' => 'american-exterminating-inc-7',
                    'date' => '03.10.2016',
                ),
            'restaurante-casa-emilio' =>
                array (
                    'name' => 'Restaurante Casa Emilio',
                    'slug' => 'restaurante-casa-emilio',
                    'date' => '04.10.2016',
                ),
            'alltech-solutions-inc' =>
                array (
                    'name' => 'Alltech Solutions',
                    'slug' => 'alltech-solutions-inc',
                    'date' => '05.10.2016',
                ),
            'garcia-ferreras-jorge-e-dr-dpm' =>
                array (
                    'name' => 'García Ferreras Jorge E. Dr. DPM',
                    'slug' => 'garcia-ferreras-jorge-e-dr-dpm',
                    'date' => '06.10.2016',
                ),
            'the-greenhouse-restaurant-food-and-wine' =>
                array (
                    'name' => 'The Greenhouse Restaurant Food & Wine',
                    'slug' => 'the-greenhouse-restaurant-food-and-wine',
                    'date' => '07.10.2016',
                ),
            'emilio-m-agrait-defillo-dmd-faapd' =>
                array (
                    'name' => 'Agrait Defillo Emilio Dr.',
                    'slug' => 'emilio-m-agrait-defillo-dmd-faapd',
                    'date' => '07.10.2016',
                ),
            'cupey-maria-montessori' =>
                array (
                    'name' => 'Cupey María Montessori',
                    'slug' => 'cupey-maria-montessori',
                    'date' => '11.10.2016',
                ),
            'ragazzi-restaurante-italiano-pizzeria' =>
                array (
                    'name' => 'Ragazzi Restaurante Italiano - Pizzería',
                    'slug' => 'ragazzi-restaurante-italiano-pizzeria',
                    'date' => '12.10.2016',
                ),
            'd-glenys-body-waxing-salon-and-spa' =>
                array (
                    'name' => 'D\' Glenys Body Waxing Salon & Spa',
                    'slug' => 'd-glenys-body-waxing-salon-and-spa',
                    'date' => '13.10.2016',
                ),
            'alans-plumbing-and-electrical-services-1' =>
                array (
                    'name' => 'Alan\'s Plumbing & Electrical Services',
                    'slug' => 'alans-plumbing-and-electrical-services-1',
                    'date' => '14.10.2016',
                ),
            'gruas-asp-auto-servicios-padilla' =>
                array (
                    'name' => 'Grúas ASP Auto Servicio Padilla',
                    'slug' => 'gruas-asp-auto-servicios-padilla',
                    'date' => '14.10.2016',
                ),
            'optiqus-vision' =>
                array (
                    'name' => 'Optiqus Vision',
                    'slug' => 'optiqus-vision',
                    'date' => '18.10.2016',
                ),
            'cj-transport-1' =>
                array (
                    'name' => 'CJ Transport',
                    'slug' => 'cj-transport-1',
                    'date' => '18.10.2016',
                ),
            'plantas-tropicales-de-puerto-rico' =>
                array (
                    'name' => 'Plantas Tropicales de Puerto Rico',
                    'slug' => 'plantas-tropicales-de-puerto-rico',
                    'date' => '19.10.2016',
                ),
            'zorzal-termite-exterminating-corp' =>
                array (
                    'name' => 'Zorzal Termite Exterminating Corp.',
                    'slug' => 'zorzal-termite-exterminating-corp',
                    'date' => '19.10.2016',
                ),
            'gomera-las-catalinas' =>
                array (
                    'name' => 'Gomera Las Catalinas',
                    'slug' => 'gomera-las-catalinas',
                    'date' => '20.10.2016',
                ),
            'healthkeepers-hospice-inc-1' =>
                array (
                    'name' => 'HealthKeepers Hospice',
                    'slug' => 'healthkeepers-hospice-inc-1',
                    'date' => '21.10.2016',
                ),
            'ortiz-almedina-and-asociados' =>
                array (
                    'name' => 'Ortiz Almedina & Asociados',
                    'slug' => 'ortiz-almedina-and-asociados',
                    'date' => '21.10.2016',
                ),
            'velazquez-carmen-dra' =>
                array (
                    'name' => 'Velázquez Carmen Dra.',
                    'slug' => 'velazquez-carmen-dra',
                    'date' => '24.10.2016',
                ),
            'monserrate-imaging-center-1' =>
                array (
                    'name' => 'Monserrate Imaging Center',
                    'slug' => 'monserrate-imaging-center-1',
                    'date' => '24.10.2016',
                ),
            'riveras-locksmith' =>
                array (
                    'name' => 'Rivera\'s Locksmith',
                    'slug' => 'riveras-locksmith',
                    'date' => '24.10.2016',
                ),
            'cemex-puerto-rico' =>
                array (
                    'name' => 'Cemex Puerto Rico',
                    'slug' => 'cemex-puerto-rico',
                    'date' => '25.10.2016',
                ),
            'miranda-and-sons-llc' =>
                array (
                    'name' => 'Miranda & Sons, LLC',
                    'slug' => 'miranda-and-sons-llc',
                    'date' => '25.10.2016',
                ),
            'all-exterminating-1' =>
                array (
                    'name' => 'All Exterminating',
                    'slug' => 'all-exterminating-1',
                    'date' => '26.10.2016',
                ),
            'el-buho-auto-repair' =>
                array (
                    'name' => 'El Búho Auto Repair',
                    'slug' => 'el-buho-auto-repair',
                    'date' => '27.10.2016',
                ),
            'ave-fenix-contratista-electrico' =>
                array (
                    'name' => 'Ave Fénix Contratista Eléctrico',
                    'slug' => 'ave-fenix-contratista-electrico',
                    'date' => '27.10.2016',
                ),
            'servicentro-baez-1' =>
                array (
                    'name' => 'Servicentro Báez',
                    'slug' => 'servicentro-baez-1',
                    'date' => '31.10.2016',
                ),
            'funeraria-alternative-and-torres-memorial' =>
                array (
                    'name' => 'Funeraria Alternative & Torres Memorial',
                    'slug' => 'funeraria-alternative-and-torres-memorial',
                    'date' => '31.10.2016',
                ),
            'jaime-rodriguez-law-office-1' =>
                array (
                    'name' => 'Jaime Rodríguez Law Office',
                    'slug' => 'jaime-rodriguez-law-office-1',
                    'date' => '15.02.2017',
                ),
            'jaime-rodriguez-law-office-4' =>
                array (
                    'name' => 'Jaime Rodríguez Law Office',
                    'slug' => 'jaime-rodriguez-law-office-4',
                    'date' => '15.02.2017',
                ),
            'jaime-rodriguez-law-office-2' =>
                array (
                    'name' => 'Jaime Rodríguez Law Office',
                    'slug' => 'jaime-rodriguez-law-office-2',
                    'date' => '15.02.2017',
                ),
            'jaime-rodriguez-law-office' =>
                array (
                    'name' => 'Jaime Rodríguez Law Office',
                    'slug' => 'jaime-rodriguez-law-office',
                    'date' => '15.02.2017',
                ),
            'jaime-rodriguez-law-office-3' =>
                array (
                    'name' => 'Jaime Rodríguez Law Office',
                    'slug' => 'jaime-rodriguez-law-office-3',
                    'date' => '15.02.2017',
                ),
            'danza-fusion' =>
                array (
                    'name' => 'Danza Fusion',
                    'slug' => 'danza-fusion',
                    'date' => '02.11.2016',
                ),
            'de-aqui-y-de-alla' =>
                array (
                    'name' => 'De Aquí y De Allá',
                    'slug' => 'de-aqui-y-de-alla',
                    'date' => '02.11.2016',
                ),
            'happy-kids-day-care-and-learning-center' =>
                array (
                    'name' => 'Happy Kids Day Care and Learning Center',
                    'slug' => 'happy-kids-day-care-and-learning-center',
                    'date' => '04.11.2016',
                ),
            'carpas-express' =>
                array (
                    'name' => 'Carpas Express',
                    'slug' => 'carpas-express',
                    'date' => '04.11.2016',
                ),
            'opti-look' =>
                array (
                    'name' => 'Opti Look',
                    'slug' => 'opti-look',
                    'date' => '07.11.2016',
                ),
            'antonios-restaurant-15' =>
                array (
                    'name' => 'Antonio\'s Restaurant',
                    'slug' => 'antonios-restaurant-15',
                    'date' => '07.11.2016',
                ),
            'milagros-de-amor-1' =>
                array (
                    'name' => 'Milagros de Amor',
                    'slug' => 'milagros-de-amor-1',
                    'date' => '09.11.2016',
                ),
            'funeraria-gonzalez-lago-2' =>
                array (
                    'name' => 'Funeraria González Lago',
                    'slug' => 'funeraria-gonzalez-lago-2',
                    'date' => '11.11.2016',
                ),
            'funeraria-gonzalez-lago-3' =>
                array (
                    'name' => 'Funeraria González Lago',
                    'slug' => 'funeraria-gonzalez-lago-3',
                    'date' => '11.11.2016',
                ),
            'enio-refrigerator-services' =>
                array (
                    'name' => 'Enio Refrigerator Services',
                    'slug' => 'enio-refrigerator-services',
                    'date' => '14.11.2016',
                ),
            'farmacia-san-jose-17' =>
                array (
                    'name' => 'Farmacia San José',
                    'slug' => 'farmacia-san-jose-17',
                    'date' => '15.11.2016',
                ),
            'farmacia-san-jose-1' =>
                array (
                    'name' => 'Farmacia San José',
                    'slug' => 'farmacia-san-jose-1',
                    'date' => '15.11.2016',
                ),
            'gruas-caez-towing' =>
                array (
                    'name' => 'Caez Towing',
                    'slug' => 'gruas-caez-towing',
                    'date' => '16.11.2016',
                ),
            'abogados-accidentes-choques-y-caidas' =>
                array (
                    'name' => 'Abogados, Accidentes, Choques y Caídas',
                    'slug' => 'abogados-accidentes-choques-y-caidas',
                    'date' => '16.11.2016',
                ),
            'jor-mar-uniforms' =>
                array (
                    'name' => 'Jor-Mar Uniforms',
                    'slug' => 'jor-mar-uniforms',
                    'date' => '17.11.2016',
                ),
            'my-happy-place-2' =>
                array (
                    'name' => 'My Happy Place',
                    'slug' => 'my-happy-place-2',
                    'date' => '19.11.2016',
                ),
            'centro-educativo-clagill' =>
                array (
                    'name' => 'Centro Educativo Clagill',
                    'slug' => 'centro-educativo-clagill',
                    'date' => '21.11.2016',
                ),
            'hogar-bendicion-de-dios' =>
                array (
                    'name' => 'Hogar Bendición de Dios',
                    'slug' => 'hogar-bendicion-de-dios',
                    'date' => '21.11.2016',
                ),
            'amos-auto-piezas' =>
                array (
                    'name' => 'Amós Auto Piezas',
                    'slug' => 'amos-auto-piezas',
                    'date' => '22.11.2016',
                ),
            'lcda-rosibel-carrasquillo-colon' =>
                array (
                    'name' => 'Carrasquillo Colón Rosibel Lcda.',
                    'slug' => 'lcda-rosibel-carrasquillo-colon',
                    'date' => '23.11.2016',
                ),
            'lcdo-luis-felipe-santiago' =>
                array (
                    'name' => 'Santiago Luis Felipe Lcdo.',
                    'slug' => 'lcdo-luis-felipe-santiago',
                    'date' => '23.11.2016',
                ),
            'farmacia-solmari' =>
                array (
                    'name' => 'Farmacia Solmari',
                    'slug' => 'farmacia-solmari',
                    'date' => '28.11.2016',
                ),
            'garaje-morales-1' =>
                array (
                    'name' => 'Garaje Morales',
                    'slug' => 'garaje-morales-1',
                    'date' => '28.11.2016',
                ),
            'lcdo-glenn-carl-james' =>
                array (
                    'name' => 'Lcdo. Glenn Carl James',
                    'slug' => 'lcdo-glenn-carl-james',
                    'date' => '28.11.2016',
                ),
            'borinquen-heavy-auto-parts' =>
                array (
                    'name' => 'Borinquen Heavy Auto Parts',
                    'slug' => 'borinquen-heavy-auto-parts',
                    'date' => '28.11.2016',
                ),
            'colegio-zaenid' =>
                array (
                    'name' => 'Colegio Zaenid',
                    'slug' => 'colegio-zaenid',
                    'date' => '29.11.2016',
                ),
            'lcdo-milton-j-garcia-ocasio' =>
                array (
                    'name' => 'García Ocasio Milton J. Lcdo.',
                    'slug' => 'lcdo-milton-j-garcia-ocasio',
                    'date' => '29.11.2016',
                ),
            'pro-health-infusion-1' =>
                array (
                    'name' => 'Pro Health Infusion',
                    'slug' => 'pro-health-infusion-1',
                    'date' => '30.11.2016',
                ),
            'farmacia-arleen' =>
                array (
                    'name' => 'Farmacia Arleen',
                    'slug' => 'farmacia-arleen',
                    'date' => '30.11.2016',
                ),
            'spazzio-bath-gallery' =>
                array (
                    'name' => 'Spazzio Bath Gallery',
                    'slug' => 'spazzio-bath-gallery',
                    'date' => '30.11.2016',
                ),
            'marbella-events-and-décor' =>
                array (
                    'name' => 'Marbella Events & Decor',
                    'slug' => 'marbella-events-and-décor',
                    'date' => '30.11.2016',
                ),
            'laboratorio-clinico-oriental' =>
                array (
                    'name' => 'Laboratorio Clínico Oriental',
                    'slug' => 'laboratorio-clinico-oriental',
                    'date' => '30.11.2016',
                ),
            'elite-vertical-supply-1' =>
                array (
                    'name' => 'Elite Vertical Supply',
                    'slug' => 'elite-vertical-supply-1',
                    'date' => '30.11.2016',
                ),
            'hogar-naguabo-home-for-the-elderly' =>
                array (
                    'name' => 'Hogar Naguabo Home for The Elderly',
                    'slug' => 'hogar-naguabo-home-for-the-elderly',
                    'date' => '02.12.2016',
                ),
            'kandeia-restaurante-grill-and-cafe' =>
                array (
                    'name' => 'Kandeia Restaurante, Grill & Café',
                    'slug' => 'kandeia-restaurante-grill-and-cafe',
                    'date' => '05.12.2016',
                ),
            'dra-mireily-martinez-llaurador' =>
                array (
                    'name' => 'Martínez Llaurador Mireily Dra.',
                    'slug' => 'dra-mireily-martinez-llaurador',
                    'date' => '06.12.2016',
                ),
            'casos-de-quiebra-roberto-figueroa-carrasquillo' =>
                array (
                    'name' => 'Casos de Quiebra Roberto Figueroa Carrasquillo',
                    'slug' => 'casos-de-quiebra-roberto-figueroa-carrasquillo',
                    'date' => '08.12.2016',
                ),
            'sabatier-tire-center' =>
                array (
                    'name' => 'Sabatier Tire Center',
                    'slug' => 'sabatier-tire-center',
                    'date' => '09.12.2016',
                ),
            'lomas-verdes-cooperativa' =>
                array (
                    'name' => 'Lomas Verdes Cooperativa',
                    'slug' => 'lomas-verdes-cooperativa',
                    'date' => '12.12.2016',
                ),
            'don-carlos-restaurant-3' =>
                array (
                    'name' => 'Don Carlos Restaurant',
                    'slug' => 'don-carlos-restaurant-3',
                    'date' => '13.12.2016',
                ),
            'centro-oftalmologico-de-arecibo' =>
                array (
                    'name' => 'Centro Oftalmológico de Arecibo',
                    'slug' => 'centro-oftalmologico-de-arecibo',
                    'date' => '13.12.2016',
                ),
            'centro-oftalmologico-de-arecibo-3' =>
                array (
                    'name' => 'Centro Oftalmológico de Arecibo',
                    'slug' => 'centro-oftalmologico-de-arecibo-3',
                    'date' => '13.12.2016',
                ),
            'nieves-quick-lunch' =>
                array (
                    'name' => 'Nieves Quick Lunch',
                    'slug' => 'nieves-quick-lunch',
                    'date' => '15.12.2016',
                ),
            'hogar-hacienda-el-paraiso-ll' =>
                array (
                    'name' => 'Hogar Hacienda El Paraíso',
                    'slug' => 'hogar-hacienda-el-paraiso-ll',
                    'date' => '16.12.2016',
                ),
            'hogar-hacienda-el-paraiso' =>
                array (
                    'name' => 'Hogar Hacienda El Paraíso',
                    'slug' => 'hogar-hacienda-el-paraiso',
                    'date' => '16.12.2016',
                ),
            'alex-albaladejo-seguros' =>
                array (
                    'name' => 'Alex Albaladejo Seguros',
                    'slug' => 'alex-albaladejo-seguros',
                    'date' => '16.12.2016',
                ),
            'colegio-san-antonio-abad' =>
                array (
                    'name' => 'Colegio San Antonio Abad',
                    'slug' => 'colegio-san-antonio-abad',
                    'date' => '16.12.2016',
                ),
            'fixit-1' =>
                array (
                    'name' => 'Fixit',
                    'slug' => 'fixit-1',
                    'date' => '21.12.2016',
                ),
            'oliver-exterminating-service-2' =>
                array (
                    'name' => 'Oliver Exterminating',
                    'slug' => 'oliver-exterminating-service-2',
                    'date' => '21.12.2016',
                ),
            'oliver-exterminating-service' =>
                array (
                    'name' => 'Oliver Exterminating',
                    'slug' => 'oliver-exterminating-service',
                    'date' => '21.12.2016',
                ),
            'oliver-exterminating-service-1' =>
                array (
                    'name' => 'Oliver Exterminating',
                    'slug' => 'oliver-exterminating-service-1',
                    'date' => '21.12.2016',
                ),
            'oliver-exterminating-service-4' =>
                array (
                    'name' => 'Oliver Exterminating',
                    'slug' => 'oliver-exterminating-service-4',
                    'date' => '21.12.2016',
                ),
            'oliver-exterminating-service-3' =>
                array (
                    'name' => 'Oliver Exterminating',
                    'slug' => 'oliver-exterminating-service-3',
                    'date' => '21.12.2016',
                ),
            'oliver-exterminating-service-5' =>
                array (
                    'name' => 'Oliver Exterminating',
                    'slug' => 'oliver-exterminating-service-5',
                    'date' => '21.12.2016',
                ),
            'garcia-trucking-services-inc' =>
                array (
                    'name' => 'García Trucking',
                    'slug' => 'garcia-trucking-services-inc',
                    'date' => '23.12.2016',
                ),
            'tus-amigos-auto-parts' =>
                array (
                    'name' => 'Tus Amigos Auto Parts',
                    'slug' => 'tus-amigos-auto-parts',
                    'date' => '27.12.2016',
                ),
            'serenite-spa' =>
                array (
                    'name' => 'Sérénité Spa',
                    'slug' => 'serenite-spa',
                    'date' => '12.01.2017',
                ),
            'cod-novelties-and-expression-cards-corp' =>
                array (
                    'name' => 'COD Novelties & Expression Cards Corp.',
                    'slug' => 'cod-novelties-and-expression-cards-corp',
                    'date' => '12.01.2017',
                ),
            'vida-abundante-christian-academy' =>
                array (
                    'name' => 'Vida Abundante Christian Academy',
                    'slug' => 'vida-abundante-christian-academy',
                    'date' => '12.01.2017',
                ),
            'iglesia-escuela-castillo-fuerte' =>
                array (
                    'name' => 'Iglesia Escuela Castillo Fuerte',
                    'slug' => 'iglesia-escuela-castillo-fuerte',
                    'date' => '13.01.2017',
                ),
            'sein-rafael-e-md-1' =>
                array (
                    'name' => 'Seín Rafael E. MD',
                    'slug' => 'sein-rafael-e-md-1',
                    'date' => '14.01.2017',
                ),
            'sein-rafael-e-md' =>
                array (
                    'name' => 'Seín Rafael E. MD',
                    'slug' => 'sein-rafael-e-md',
                    'date' => '14.01.2017',
                ),
            'merced-air-conditioning' =>
                array (
                    'name' => 'Merced Air Conditioning',
                    'slug' => 'merced-air-conditioning',
                    'date' => '17.01.2017',
                ),
            'dra-annelisse-figueroa-sanchez' =>
                array (
                    'name' => 'Figueroa Sánchez Annelisse Dra.',
                    'slug' => 'dra-annelisse-figueroa-sanchez',
                    'date' => '17.01.2017',
                ),
            'empire-gas-slash-empresas-de-gas' =>
                array (
                    'name' => 'Empire Gas/Empresas de Gas',
                    'slug' => 'empire-gas-slash-empresas-de-gas',
                    'date' => '17.01.2017',
                ),
            'fruits-and-flowers-shop' =>
                array (
                    'name' => 'Fruits & Flowers Shop',
                    'slug' => 'fruits-and-flowers-shop',
                    'date' => '18.01.2017',
                ),
            'gomera-de-jesus-y-servicio-de-grua' =>
                array (
                    'name' => 'Gomera De Jesús y Servicio de Grúas',
                    'slug' => 'gomera-de-jesus-y-servicio-de-grua',
                    'date' => '19.01.2017',
                ),
            'al-hanny-flower' =>
                array (
                    'name' => 'Al Hanny Flower',
                    'slug' => 'al-hanny-flower',
                    'date' => '20.01.2017',
                ),
            'laboratorio-clinico-rodriguez-6' =>
                array (
                    'name' => 'Laboratorio Clínico Rodríguez',
                    'slug' => 'laboratorio-clinico-rodriguez-6',
                    'date' => '25.01.2017',
                ),
            'laboratorio-clinico-rodriguez-7' =>
                array (
                    'name' => 'Laboratorio Clínico Rodríguez',
                    'slug' => 'laboratorio-clinico-rodriguez-7',
                    'date' => '25.01.2017',
                ),
            'laboratorio-clinico-rodriguez-arecibo' =>
                array (
                    'name' => 'Laboratorio Clínico Rodríguez',
                    'slug' => 'laboratorio-clinico-rodriguez-arecibo',
                    'date' => '25.01.2017',
                ),
            'farmacia-el-norte' =>
                array (
                    'name' => 'Farmacia El Norte',
                    'slug' => 'farmacia-el-norte',
                    'date' => '25.01.2017',
                ),
            'panaderia-y-reposteria-la-asturiana' =>
                array (
                    'name' => 'Panadería y Repostería La Asturiana',
                    'slug' => 'panaderia-y-reposteria-la-asturiana',
                    'date' => '26.01.2017',
                ),
            'farmacia-marena' =>
                array (
                    'name' => 'Farmacia Marena',
                    'slug' => 'farmacia-marena',
                    'date' => '26.01.2017',
                ),
            'clinica-veterinaria-mi-mascota' =>
                array (
                    'name' => 'Clínica Veterinaria Mi Mascota',
                    'slug' => 'clinica-veterinaria-mi-mascota',
                    'date' => '26.01.2017',
                ),
            'caguax-tire' =>
                array (
                    'name' => 'Caguax Tire',
                    'slug' => 'caguax-tire',
                    'date' => '28.01.2017',
                ),
            'estudio-de-asesoria-legal-lcdo-carlos-rodriguez-garcia' =>
                array (
                    'name' => 'Estudio de Asesoría Legal Lcdo. Carlos Rodríguez García',
                    'slug' => 'estudio-de-asesoria-legal-lcdo-carlos-rodriguez-garcia',
                    'date' => '30.01.2017',
                ),
            'bufete-montalvo-burgos' =>
                array (
                    'name' => 'Bufete Montalvo Burgos',
                    'slug' => 'bufete-montalvo-burgos',
                    'date' => '31.01.2017',
                ),
            'erc-entertainment-inc' =>
                array (
                    'name' => 'ERC Entertainment Inc.',
                    'slug' => 'erc-entertainment-inc',
                    'date' => '01.02.2017',
                ),
            'jc-tire-kingdom-and-auto-service' =>
                array (
                    'name' => 'JC Tire Kingdom & Auto Service',
                    'slug' => 'jc-tire-kingdom-and-auto-service',
                    'date' => '01.02.2017',
                ),
            'jc-tire-kingdom-and-auto-service-1' =>
                array (
                    'name' => 'JC Tire Kingdom & Auto Service',
                    'slug' => 'jc-tire-kingdom-and-auto-service-1',
                    'date' => '01.02.2017',
                ),
            'le-paris-esthetic' =>
                array (
                    'name' => 'Le Paris Esthetic',
                    'slug' => 'le-paris-esthetic',
                    'date' => '02.02.2017',
                ),
            'ortiz-kitchen-manufacturing-gabinetes' =>
                array (
                    'name' => 'Ortíz Kitchen Manufacturing-Gabinetes',
                    'slug' => 'ortiz-kitchen-manufacturing-gabinetes',
                    'date' => '03.02.2017',
                ),
            'sabor-y-sazon' =>
                array (
                    'name' => 'Sabor y Sazón',
                    'slug' => 'sabor-y-sazon',
                    'date' => '03.02.2017',
                ),
            'brusi-beach-resort-and-restaurant' =>
                array (
                    'name' => 'Brusi Beach Resort & Restaurant',
                    'slug' => 'brusi-beach-resort-and-restaurant',
                    'date' => '07.02.2017',
                ),
            'elwood-tire-center' =>
                array (
                    'name' => 'Elwood Tire Center',
                    'slug' => 'elwood-tire-center',
                    'date' => '07.02.2017',
                ),
            'em-tire-distributor-inc' =>
                array (
                    'name' => 'EM Tire Distributor Inc.',
                    'slug' => 'em-tire-distributor-inc',
                    'date' => '07.02.2017',
                ),
            'convention-services-promotions-corp' =>
                array (
                    'name' => 'Convention Services Promotions Corp.',
                    'slug' => 'convention-services-promotions-corp',
                    'date' => '07.02.2017',
                ),
            'arecibo-light-sport-aviation' =>
                array (
                    'name' => 'Arecibo Light Sport Aviation',
                    'slug' => 'arecibo-light-sport-aviation',
                    'date' => '08.02.2017',
                ),
            'rgf-law-firm-accidentes-caidas-choques-danos-practica-civil-notarios' =>
                array (
                    'name' => 'RGF Law Firm',
                    'slug' => 'rgf-law-firm-accidentes-caidas-choques-danos-practica-civil-notarios',
                    'date' => '09.02.2017',
                ),
            'laboratorio-clinico-colon-1' =>
                array (
                    'name' => 'Laboratorio Clínico Colón',
                    'slug' => 'laboratorio-clinico-colon-1',
                    'date' => '10.02.2017',
                ),
            'laboratorio-clinico-colon' =>
                array (
                    'name' => 'Laboratorio Clínico Colón',
                    'slug' => 'laboratorio-clinico-colon',
                    'date' => '10.02.2017',
                ),
            'laboratorio-clinico-colon-number-2' =>
                array (
                    'name' => 'Laboratorio Clínico Colón',
                    'slug' => 'laboratorio-clinico-colon-number-2',
                    'date' => '10.02.2017',
                ),
            'laboratorio-clinico-colon-number-3' =>
                array (
                    'name' => 'Laboratorio Clínico Colón',
                    'slug' => 'laboratorio-clinico-colon-number-3',
                    'date' => '10.02.2017',
                ),
            'cdi-emmanuel-1' =>
                array (
                    'name' => 'CDI Emmanuel',
                    'slug' => 'cdi-emmanuel-1',
                    'date' => '10.02.2017',
                ),
            'iphone-city-repair-shop' =>
                array (
                    'name' => 'Iphone City',
                    'slug' => 'iphone-city-repair-shop',
                    'date' => '13.02.2017',
                ),
            'kings-restaurant-pizzeria-and-bakery' =>
                array (
                    'name' => 'King´s Restaurant Pizzeria & Bakery',
                    'slug' => 'kings-restaurant-pizzeria-and-bakery',
                    'date' => '16.02.2017',
                ),
            'tpx-uniforms-inc-2' =>
                array (
                    'name' => 'TPX Uniforms Inc',
                    'slug' => 'tpx-uniforms-inc-2',
                    'date' => '18.02.2017',
                ),
            'the-san-juan-spa' =>
                array (
                    'name' => 'The San Juan Spa',
                    'slug' => 'the-san-juan-spa',
                    'date' => '21.02.2017',
                ),
            'gomera-vip-tire-shop' =>
                array (
                    'name' => 'Gomera VIP Tire Shop',
                    'slug' => 'gomera-vip-tire-shop',
                    'date' => '21.02.2017',
                ),
            'dr-andres-emanuelli-anzalotta' =>
                array (
                    'name' => 'Emanuelli Anzalotta Andrés Dr.',
                    'slug' => 'dr-andres-emanuelli-anzalotta',
                    'date' => '21.02.2017',
                ),
            'farmacia-velez' =>
                array (
                    'name' => 'Farmacia Vélez',
                    'slug' => 'farmacia-velez',
                    'date' => '21.02.2017',
                ),
            'lcdo-jose-a-leon-landrau' =>
                array (
                    'name' => 'León Landrau José A.',
                    'slug' => 'lcdo-jose-a-leon-landrau',
                    'date' => '21.02.2017',
                ),
            'dr-roberto-davila-de-pedro-dermatology-practice' =>
                array (
                    'name' => 'Dr. Roberto Dávila De Pedro Dermatology Practice.',
                    'slug' => 'dr-roberto-davila-de-pedro-dermatology-practice',
                    'date' => '23.02.2017',
                ),
            'bruno-steel-1' =>
                array (
                    'name' => 'Bruno Steel',
                    'slug' => 'bruno-steel-1',
                    'date' => '23.02.2017',
                ),
            'carolas-home-care' =>
                array (
                    'name' => 'Carola\'s Home Care',
                    'slug' => 'carolas-home-care',
                    'date' => '23.02.2017',
                ),
            'lcda-maribel-g-rubio-bello' =>
                array (
                    'name' => 'Rubio Bello Maribel G. Lcda.',
                    'slug' => 'lcda-maribel-g-rubio-bello',
                    'date' => '24.02.2017',
                ),
            'lcdo-josean-m-rivera-and-asociados' =>
                array (
                    'name' => 'Lcdo. Josean M. Rivera & Asocioados',
                    'slug' => 'lcdo-josean-m-rivera-and-asociados',
                    'date' => '24.02.2017',
                ),
            'ineabelle-sola-and-asociados' =>
                array (
                    'name' => 'Ineabelle Solá & Asociados',
                    'slug' => 'ineabelle-sola-and-asociados',
                    'date' => '24.02.2017',
                ),
            'hogar-san-alfonso' =>
                array (
                    'name' => 'Hogar San Alfonso',
                    'slug' => 'hogar-san-alfonso',
                    'date' => '24.02.2017',
                ),
            'tire-solutions-4' =>
                array (
                    'name' => 'Tire Solutions',
                    'slug' => 'tire-solutions-4',
                    'date' => '24.02.2017',
                ),
            'paco-buena-cocina' =>
                array (
                    'name' => 'Paco Buena Cocina',
                    'slug' => 'paco-buena-cocina',
                    'date' => '01.03.2017',
                ),
            'profesionales-legales' =>
                array (
                    'name' => 'Profesionales Legales',
                    'slug' => 'profesionales-legales',
                    'date' => '02.03.2017',
                ),
            'fonseca-salgado-carlos-a-dr-facog' =>
                array (
                    'name' => 'Carlos A. Salgado',
                    'slug' => 'fonseca-salgado-carlos-a-dr-facog',
                    'date' => '03.03.2017',
                ),
            'farmacia-valle-tolima' =>
                array (
                    'name' => 'Farmacia Valle Tolima/Farmacia Santa Juana',
                    'slug' => 'farmacia-valle-tolima',
                    'date' => '03.03.2017',
                ),
            'farmacia-santa-juana' =>
                array (
                    'name' => 'Farmacia Valle Tolima/Farmacia Santa Juana',
                    'slug' => 'farmacia-santa-juana',
                    'date' => '03.03.2017',
                ),
            'ray-construction-dios-es-amor' =>
                array (
                    'name' => 'Ray Construction',
                    'slug' => 'ray-construction-dios-es-amor',
                    'date' => '07.03.2017',
                ),
            'cell-comunication-inc' =>
                array (
                    'name' => 'Cell Comunication, Inc.',
                    'slug' => 'cell-comunication-inc',
                    'date' => '08.03.2017',
                ),
            'economico-auto-parts-number-2' =>
                array (
                    'name' => 'Económico Auto Parts',
                    'slug' => 'economico-auto-parts-number-2',
                    'date' => '08.03.2017',
                ),
            'economico-auto-parts' =>
                array (
                    'name' => 'Económico Auto Parts',
                    'slug' => 'economico-auto-parts',
                    'date' => '08.03.2017',
                ),
            'bufete-medina-and-medina-csp' =>
                array (
                    'name' => 'Bufete Medina & Medina C.S.P.',
                    'slug' => 'bufete-medina-and-medina-csp',
                    'date' => '09.03.2017',
                ),
            'hogar-maria-de-isabela-inc' =>
                array (
                    'name' => 'Hogar María de Isabela Inc.',
                    'slug' => 'hogar-maria-de-isabela-inc',
                    'date' => '14.03.2017',
                ),
            'oceans-dental-1' =>
                array (
                    'name' => 'Oceans Dental',
                    'slug' => 'oceans-dental-1',
                    'date' => '14.03.2017',
                ),
            'oceans-dental-2' =>
                array (
                    'name' => 'Oceans Dental',
                    'slug' => 'oceans-dental-2',
                    'date' => '14.03.2017',
                ),
            'la-familia-tire-inc-alineamiento-hnos-ibarra' =>
                array (
                    'name' => 'La Familia Tire Inc. / Alineamiento Hnos. Ibarra',
                    'slug' => 'la-familia-tire-inc-alineamiento-hnos-ibarra',
                    'date' => '15.03.2017',
                ),
            'lcdo-cesar-hernandez-gonzalez' =>
                array (
                    'name' => 'Hernández González César',
                    'slug' => 'lcdo-cesar-hernandez-gonzalez',
                    'date' => '15.03.2017',
                ),
            'flowers-by-john' =>
                array (
                    'name' => 'Flowers by John',
                    'slug' => 'flowers-by-john',
                    'date' => '16.03.2017',
                ),
            'el-ikokal-con-pescaderia' =>
                array (
                    'name' => 'El Ikokal Con Pescadería',
                    'slug' => 'el-ikokal-con-pescaderia',
                    'date' => '16.03.2017',
                ),
            'lic-hector-a-pomales-otero-abogado-notario' =>
                array (
                    'name' => 'Pomales Otero Héctor A. Abogado-Notario',
                    'slug' => 'lic-hector-a-pomales-otero-abogado-notario',
                    'date' => '16.03.2017',
                ),
            '4-all-insurance-services-corp' =>
                array (
                    'name' => '4 All Insurance Services Corp.',
                    'slug' => '4-all-insurance-services-corp',
                    'date' => '17.03.2017',
                ),
            '4-all-insurance-services-corp-1' =>
                array (
                    'name' => '4 All Insurance Services Corp.',
                    'slug' => '4-all-insurance-services-corp-1',
                    'date' => '17.03.2017',
                ),
            'laboratorio-clinico-y-bacteriologico-jaimar' =>
                array (
                    'name' => 'Laboratorio Clínico y Bacteriológico Jaimar',
                    'slug' => 'laboratorio-clinico-y-bacteriologico-jaimar',
                    'date' => '17.03.2017',
                ),
            'farmacia-campo-alegre' =>
                array (
                    'name' => 'Farmacia Campo Alegre',
                    'slug' => 'farmacia-campo-alegre',
                    'date' => '18.03.2017',
                ),
            'zega-covers-covermania' =>
                array (
                    'name' => 'Zega Covers',
                    'slug' => 'zega-covers-covermania',
                    'date' => '20.03.2017',
                ),
            'vesalius-natural-wellness' =>
                array (
                    'name' => 'Vesalius Natural Wellness',
                    'slug' => 'vesalius-natural-wellness',
                    'date' => '20.03.2017',
                ),
            'clinica-de-terapia-fisica-milagros-otero-iturregui-rehab-center' =>
                array (
                    'name' => 'CLINICA DE TERAPIA FISICA MILAGROS OTERO (Iturregui Rehab Center)',
                    'slug' => 'clinica-de-terapia-fisica-milagros-otero-iturregui-rehab-center',
                    'date' => '22.03.2017',
                ),
            'licenciado-ramon-llorach' =>
                array (
                    'name' => 'Licenciado Ramón Llorach',
                    'slug' => 'licenciado-ramon-llorach',
                    'date' => '23.03.2017',
                ),
            'santiago-air-conditioning' =>
                array (
                    'name' => 'Santiago Air Conditioning',
                    'slug' => 'santiago-air-conditioning',
                    'date' => '24.03.2017',
                ),
            'ponce-wholesale' =>
                array (
                    'name' => 'Ponce Wholesale',
                    'slug' => 'ponce-wholesale',
                    'date' => '24.03.2017',
                ),
            'condado-travel' =>
                array (
                    'name' => 'Condado Travel',
                    'slug' => 'condado-travel',
                    'date' => '27.03.2017',
                ),
            'bedford-and-brooklyn-bakers' =>
                array (
                    'name' => 'Bedford Brooklyn Bakers',
                    'slug' => 'bedford-and-brooklyn-bakers',
                    'date' => '30.03.2017',
                ),
            'centro-maderas-anasco-y-camuy-1' =>
                array (
                    'name' => 'Centro Maderas Añasco y Camuy',
                    'slug' => 'centro-maderas-anasco-y-camuy-1',
                    'date' => '30.03.2017',
                ),
            'audiology-clinics-of-puerto-rico' =>
                array (
                    'name' => 'Audiology Clinics of Puerto Rico',
                    'slug' => 'audiology-clinics-of-puerto-rico',
                    'date' => '30.03.2017',
                ),
            'audiology-clinics-of-puerto-rico-1' =>
                array (
                    'name' => 'Audiology Clinics of Puerto Rico',
                    'slug' => 'audiology-clinics-of-puerto-rico-1',
                    'date' => '30.03.2017',
                ),
            'dra-wanda-casiano-quiles-y-dr-luis-o-negron' =>
                array (
                    'name' => 'Western Hematology Oncology Group/ Dra Wanda Casiano y Dr Luis O Negron',
                    'slug' => 'dra-wanda-casiano-quiles-y-dr-luis-o-negron',
                    'date' => '30.03.2017',
                ),
            'tun-tun-towing-service' =>
                array (
                    'name' => 'Tun Tun Towing Service',
                    'slug' => 'tun-tun-towing-service',
                    'date' => '30.03.2017',
                ),
            'laboratorio-clinico-profesional-emanuel' =>
                array (
                    'name' => 'Laboratorio Clínico Profesional Emanuel',
                    'slug' => 'laboratorio-clinico-profesional-emanuel',
                    'date' => '30.03.2017',
                ),
            'magayan-patio' =>
                array (
                    'name' => 'Magayán Patio',
                    'slug' => 'magayan-patio',
                    'date' => '31.03.2017',
                ),
            'c-and-c-lab-1' =>
                array (
                    'name' => 'C & C Lab',
                    'slug' => 'c-and-c-lab-1',
                    'date' => '31.03.2017',
                ),
            'mayaguez-destape' =>
                array (
                    'name' => 'Mayagüez Destape',
                    'slug' => 'mayaguez-destape',
                    'date' => '31.03.2017',
                ),
            'kamoli' =>
                array (
                    'name' => 'Kamoli',
                    'slug' => 'kamoli',
                    'date' => '04.04.2017',
                ),
            'farmacia-jireh' =>
                array (
                    'name' => 'Farmacia Jireh',
                    'slug' => 'farmacia-jireh',
                    'date' => '04.04.2017',
                ),
            'laboratorio-clinico-sagrada-familia' =>
                array (
                    'name' => 'Laboratorio Clínico Sagrada Familia',
                    'slug' => 'laboratorio-clinico-sagrada-familia',
                    'date' => '05.04.2017',
                ),
            'laboratorio-clinico-gaudier' =>
                array (
                    'name' => 'Laboratorio Clínico Gaudier',
                    'slug' => 'laboratorio-clinico-gaudier',
                    'date' => '06.04.2017',
                ),
            'cooperativa-de-seguros-multiples-de-puerto-rico-17' =>
                array (
                    'name' => 'Seguros Puerto Rico',
                    'slug' => 'cooperativa-de-seguros-multiples-de-puerto-rico-17',
                    'date' => '07.04.2017',
                ),
        );
        
        return $categories;
    }
}
