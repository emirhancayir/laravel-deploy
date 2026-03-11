<?php

namespace Database\Seeders;

use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Database\Seeder;

class MahalleSeeder extends Seeder
{
    public function run(): void
    {
        $mahalleler = [
            // İstanbul - Kadıköy
            'Kadıköy' => ['Acıbadem', 'Bostancı', 'Caddebostan', 'Caferağa', 'Fenerbahçe', 'Feneryolu', 'Fikirtepe', 'Göztepe', 'Hasanpaşa', 'Koşuyolu', 'Kozyatağı', 'Moda', 'Osmanağa', 'Rasimpaşa', 'Sahrayıcedit', 'Suadiye', 'Zühtüpaşa', 'Erenköy', 'Merdivenköy'],
            // İstanbul - Beşiktaş
            'Beşiktaş' => ['Abbasağa', 'Akatlar', 'Arnavutköy', 'Bebek', 'Cihannüma', 'Dikilitaş', 'Etiler', 'Gayrettepe', 'Konaklar', 'Kuruçeşme', 'Levazım', 'Levent', 'Mecidiye', 'Muradiye', 'Nisbetiye', 'Ortaköy', 'Sinanpaşa', 'Türkali', 'Ulus', 'Vişnezade', 'Yıldız'],
            // İstanbul - Şişli
            'Şişli' => ['Bozkurt', 'Cumhuriyet', 'Dikilitaş', 'Ergenekon', 'Esentepe', 'Feriköy', 'Fulya', 'Gülbahar', 'Halaskargazi', 'Harbiye', 'Halıcıoğlu', 'İnönü', 'Kaptanpaşa', 'Kuştepe', 'Mahmut Şevket Paşa', 'Mecidiyeköy', 'Merkez', 'Meşrutiyet', 'Paşa', 'Teşvikiye', 'Yayla'],
            // İstanbul - Bakırköy
            'Bakırköy' => ['Ataköy 1. Kısım', 'Ataköy 2-5-6. Kısım', 'Ataköy 3-4-11. Kısım', 'Ataköy 7-8-9-10. Kısım', 'Basınköy', 'Cevizlik', 'Kartaltepe', 'Osmaniye', 'Sakızağacı', 'Şenlikköy', 'Yenimahalle', 'Yeşilköy', 'Yeşilyurt', 'Zuhuratbaba'],
            // İstanbul - Fatih
            'Fatih' => ['Aksaray', 'Alemdar', 'Ali Kuşçu', 'Atikali', 'Ayvansaray', 'Balat', 'Binbirdirek', 'Cankurtaran', 'Cerrahpaşa', 'Eminönü', 'Fener', 'Haseki', 'Karagümrük', 'Kumkapı', 'Laleli', 'Molla Gürani', 'Nişanca', 'Saraçhane', 'Sultanahmet', 'Süleymaniye', 'Topkapı', 'Unkapanı', 'Vefa', 'Yavuz Sultan Selim', 'Zeyrek'],
            // İstanbul - Üsküdar
            'Üsküdar' => ['Acıbadem', 'Ahmediye', 'Altunizade', 'Aziz Mahmut Hüdayi', 'Bahçelievler', 'Barbaros', 'Beylerbeyi', 'Bulgurlu', 'Burhaniye', 'Çengelköy', 'Ferah', 'İcadiye', 'Kandilli', 'Kısıklı', 'Kuzguncuk', 'Küçüksu', 'Mimar Sinan', 'Salacak', 'Selimiye', 'Ünalan', 'Validei Atik', 'Yavuztürk'],
            // İstanbul - Ümraniye
            'Ümraniye' => ['Adem Yavuz', 'Altınşehir', 'Armağanevler', 'Aşağı Dudullu', 'Atakent', 'Çakmak', 'Çamlık', 'Dumlupınar', 'Elmalıkent', 'Esenevler', 'Esenkent', 'Fatih Sultan Mehmet', 'Hekimbaşı', 'Ihlamurkuyu', 'İnkılap', 'İstiklal', 'Kazım Karabekir', 'Madenler', 'Mehmet Akif', 'Namık Kemal', 'Necip Fazıl', 'Saray', 'Site', 'Şerifali', 'Tantavi', 'Tatlısu', 'Tepeüstü', 'Topağacı', 'Yamanevler', 'Yeni Çamlıca', 'Yukarı Dudullu'],
            // İstanbul - Kartal
            'Kartal' => ['Atalar', 'Cevizli', 'Cumhuriyet', 'Çavuşoğlu', 'Esentepe', 'Gümüşpınar', 'Hürriyet', 'Karlıktepe', 'Kordonboyu', 'Orhantepe', 'Petrol İş', 'Soğanlık', 'Topselvi', 'Uğur Mumcu', 'Yakacık', 'Yukarı'],
            // İstanbul - Maltepe
            'Maltepe' => ['Altıntepe', 'Altıyol', 'Aydınevler', 'Bağlarbaşı', 'Başıbüyük', 'Büyükbakkalköy', 'Cevizli', 'Çınar', 'Esenkent', 'Feyzullah', 'Fındıklı', 'Girne', 'Gülsuyu', 'Gülensu', 'İdealtepe', 'Küçükyalı', 'Yalı', 'Zümrütevler'],
            // İstanbul - Pendik
            'Pendik' => ['Ahmet Yesevi', 'Batı', 'Çamçeşme', 'Çınardere', 'Dolayoba', 'Doğu', 'Emirli', 'Ertuğrulgazi', 'Esenler', 'Esenyalı', 'Fevzi Çakmak', 'Güllübağlar', 'Güzelyalı', 'Harmandere', 'Kaynarca', 'Kurtköy', 'Orta', 'Ramazanoğlu', 'Sanayi', 'Sapanbağları', 'Şeyhli', 'Velibaba', 'Yayalar', 'Yenimahalle', 'Yenişehir'],

            // Ankara - Çankaya
            'Çankaya' => ['Ayrancı', 'Bahçelievler', 'Balgat', 'Birlik', 'Cebeci', 'Çayyolu', 'Dikmen', 'Emek', 'Esat', 'Gaziosmanpaşa', 'Kavaklıdere', 'Kızılay', 'Kocatepe', 'Kolej', 'Küçükesat', 'Maltepe', 'Mebusevleri', 'Meşrutiyet', 'Mustafa Kemal', 'Öveçler', 'Seyranbağları', 'Ümitköy', 'Yıldız', 'Yukarı Ayrancı'],
            // Ankara - Keçiören
            'Keçiören' => ['Aktepe', 'Atapark', 'Ayvansaray', 'Bağlum', 'Basınevleri', 'Çaldıran', 'Emrah', 'Etlik', 'Esertepe', 'Güçlükaya', 'İncirli', 'Kalaba', 'Kamil Ocak', 'Kanuni', 'Karşıyaka', 'Kavacık', 'Kuşcağız', 'Ufuktepe', 'Yayla', 'Yeşiltepe'],
            // Ankara - Yenimahalle
            'Yenimahalle' => ['Alacaatlı', 'Anadolu', 'Anayurt', 'Barış', 'Batıkent', 'Cardak', 'Demetevler', 'Ergazi', 'Gayret', 'İvedik', 'Karşıyaka', 'Macun', 'Mehmet Akif Ersoy', 'Ostim', 'Pamuklar', 'Ragıp Tüzün', 'Serhat', 'Şentepe', 'Yuva'],

            // İzmir - Konak
            'Konak' => ['Agora', 'Akdeniz', 'Alsancak', 'Altay', 'Basmane', 'Bozkaya', 'Çankaya', 'Eşrefpaşa', 'Göztepe', 'Güneşli', 'Güzelyalı', 'İkiçeşmelik', 'İsmet Kaptan', 'Kadifekale', 'Kahramanlar', 'Karantina', 'Kemeraltı', 'Kültür', 'Küçükyalı', 'Mersinli', 'Mimar Kemalettin', 'Pazaryeri', 'Umurbey', 'Varyant', 'Yenişehir'],
            // İzmir - Karşıyaka
            'Karşıyaka' => ['Aksoy', 'Alaybey', 'Atakent', 'Bahçelievler', 'Bahriye Üçok', 'Bostanlı', 'Cumhuriyet', 'Demirköprü', 'Donanmacı', 'Goncalar', 'Girne', 'İnönü', 'Latife Hanım', 'Mavişehir', 'Nergiz', 'Örnekköy', 'Şemikler', 'Tersane', 'Tuna', 'Yalı', 'Yamanlar', 'Zübeyde Hanım'],
            // İzmir - Bornova
            'Bornova' => ['Altındağ', 'Birlik', 'Çamdibi', 'Doğanlar', 'Erzene', 'Evka 3', 'Evka 4', 'Kazımdirik', 'Kemalpaşa', 'Kızılay', 'Laka', 'Mevlana', 'Naldöken', 'Rafetpaşa', 'Serintepe', 'Yeşilova', 'Yunus Emre'],

            // Bursa - Nilüfer
            'Nilüfer' => ['19 Mayıs', '23 Nisan', 'Alaaddinbey', 'Altınşehir', 'Ataevler', 'Balat', 'Beşevler', 'Çamlıca', 'Ertuğrul', 'Esentepe', 'Fethiye', 'Görükle', 'İhsaniye', 'Karaman', 'Konak', 'Odunluk', 'Özlüce', 'Üçevler'],
            // Bursa - Osmangazi
            'Osmangazi' => ['Alemdar', 'Çekirge', 'Demirtaşpaşa', 'Doğanbey', 'Emek', 'Gaziakdemir', 'Hamitler', 'Hürriyet', 'Kükürtlü', 'Muradiye', 'Panayır', 'Santral', 'Soğanlı', 'Yeşilyayla', 'Yıldırım Beyazıt'],

            // Antalya - Muratpaşa
            'Muratpaşa' => ['Bahçelievler', 'Balbey', 'Cumhuriyet', 'Deniz', 'Elmalı', 'Etiler', 'Fener', 'Gebizli', 'Güzeloba', 'Kızılarık', 'Kızıltoprak', 'Konuksever', 'Lara', 'Memurevleri', 'Sinan', 'Şirinyalı', 'Tahılpazarı', 'Varlık', 'Yenigün', 'Yeşildere', 'Yeşilova', 'Zerdalilik'],
            // Antalya - Kepez
            'Kepez' => ['Altınova', 'Antalya Organize Sanayi', 'Baraj', 'Düden', 'Emek', 'Fabrikalar', 'Göksu', 'Güneş', 'Kepez', 'Kültür', 'Santral', 'Sütçüler', 'Teomanpaşa', 'Ünsal', 'Varsak', 'Yenidoğan', 'Yeşilyurt'],
        ];

        foreach ($mahalleler as $ilceAdi => $mahalleListesi) {
            $ilce = Ilce::where('ilce_adi', $ilceAdi)->first();

            if ($ilce) {
                foreach ($mahalleListesi as $mahalleAdi) {
                    Mahalle::firstOrCreate([
                        'ilce_id' => $ilce->id,
                        'mahalle_adi' => $mahalleAdi,
                    ]);
                }
            }
        }
    }
}
