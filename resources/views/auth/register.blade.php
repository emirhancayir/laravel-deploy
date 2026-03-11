@extends('layouts.app')

@section('title', 'Kayıt Ol - ' . config('app.name'))

@push('styles')
<style>
.auth-page {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, rgba(17, 153, 142, 0.1) 0%, rgba(56, 239, 125, 0.1) 100%);
}
.auth-card {
    width: 100%;
    max-width: 550px;
    animation: fadeInUp 0.6s ease-out;
}
.auth-logo {
    text-align: center;
    margin-bottom: 30px;
}
.auth-logo i {
    font-size: 3rem;
    background: var(--secondary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 15px;
    display: block;
}
.auth-logo h2 {
    font-size: 1.8rem;
    color: var(--text-primary);
    margin-bottom: 8px;
}
.auth-logo p {
    color: var(--text-secondary);
    font-size: 0.95rem;
}
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
@media (max-width: 500px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
.agreement-section {
    background: var(--bg-dark);
    padding: 20px;
    border-radius: var(--radius);
    margin: 20px 0;
    box-shadow: var(--shadow-concave-sm);
}
.agreement-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 15px;
}
.agreement-item:last-child {
    margin-bottom: 0;
}
.agreement-item input[type="checkbox"] {
    margin-top: 3px;
    width: 20px;
    height: 20px;
    accent-color: var(--secondary);
    cursor: pointer;
}
.agreement-item label {
    font-size: 0.9rem;
    line-height: 1.5;
    color: var(--text-secondary);
}
.agreement-item a {
    color: var(--primary);
    font-weight: 500;
    text-decoration: underline;
}
.agreement-item a:hover {
    color: var(--primary-dark);
}
.seller-cta {
    margin-top: 25px;
    padding: 20px;
    background: var(--primary-gradient);
    border-radius: var(--radius);
    text-align: center;
    color: white;
    box-shadow: var(--shadow-convex-sm);
}
.seller-cta i {
    font-size: 1.5rem;
    margin-bottom: 10px;
    display: block;
}
.seller-cta p {
    margin: 5px 0;
}
.seller-cta a {
    color: white;
    font-weight: 600;
    text-decoration: underline;
}
.divider {
    display: flex;
    align-items: center;
    margin: 25px 0;
    color: var(--text-light);
}
.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--bg-dark);
}
.divider span {
    padding: 0 15px;
    font-size: 0.85rem;
}
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
    overflow-y: auto;
    backdrop-filter: blur(5px);
}
.modal-content {
    background: var(--bg-color);
    max-width: 700px;
    margin: 50px auto;
    padding: 30px;
    border-radius: var(--radius-lg);
    position: relative;
    box-shadow: var(--shadow-float);
    animation: fadeInUp 0.3s ease-out;
}
.close-modal {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 28px;
    cursor: pointer;
    color: var(--text-secondary);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: var(--transition);
}
.close-modal:hover {
    background: var(--bg-dark);
    color: var(--danger);
}
.modal-content h2 {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--bg-dark);
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.modal-body {
    max-height: 60vh;
    overflow-y: auto;
    padding-right: 10px;
}
.modal-body h3 {
    color: var(--primary);
    margin: 20px 0 10px;
    font-size: 1.1rem;
}
.modal-body ul {
    margin-left: 20px;
    margin-bottom: 15px;
}
.modal-body li {
    margin-bottom: 8px;
    line-height: 1.5;
    color: var(--text-secondary);
}
.modal-body p {
    line-height: 1.7;
    margin-bottom: 12px;
    color: var(--text-secondary);
}
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="form-container neu-card">
            <div class="auth-logo">
                <i class="fas fa-user-plus"></i>
                <h2>Hesap Oluştur</h2>
                <p>Alışverişe başlamak için kayıt olun</p>
            </div>

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" data-validate>
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label for="ad">
                            <i class="fas fa-user"></i> Ad *
                        </label>
                        <input type="text" id="ad" name="ad" class="neu-input" value="{{ old('ad') }}" placeholder="Adınız" required>
                    </div>
                    <div class="form-group">
                        <label for="soyad">
                            <i class="fas fa-user"></i> Soyad *
                        </label>
                        <input type="text" id="soyad" name="soyad" class="neu-input" value="{{ old('soyad') }}" placeholder="Soyadınız" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> E-posta Adresi *
                    </label>
                    <input type="email" id="email" name="email" class="neu-input" value="{{ old('email') }}" placeholder="ornek@email.com" required>
                </div>

                <div class="form-group">
                    <label for="telefon">
                        <i class="fas fa-phone"></i> Telefon (Opsiyonel)
                    </label>
                    <input type="tel" id="telefon" name="telefon" class="neu-input" value="{{ old('telefon') }}" placeholder="05XX XXX XX XX">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Şifre *
                        </label>
                        <input type="password" id="password" name="password" class="neu-input" placeholder="••••••••" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">
                            <i class="fas fa-lock"></i> Şifre Tekrar *
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="neu-input" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="agreement-section">
                    <div class="agreement-item">
                        <input type="checkbox" id="kvkk" name="kvkk" {{ old('kvkk') ? 'checked' : '' }}>
                        <label for="kvkk">
                            <a href="#" onclick="showModal('kvkk-modal'); return false;">KVKK Aydınlatma Metni</a>'ni okudum ve kabul ediyorum. *
                        </label>
                    </div>
                    <div class="agreement-item">
                        <input type="checkbox" id="kullanim_sartlari" name="kullanim_sartlari" {{ old('kullanim_sartlari') ? 'checked' : '' }}>
                        <label for="kullanim_sartlari">
                            <a href="#" onclick="showModal('kullanim-modal'); return false;">Kullanım Şartları</a>'nı okudum ve kabul ediyorum. *
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-secondary btn-block">
                    <i class="fas fa-user-plus"></i> Kayıt Ol
                </button>
            </form>

            <div class="divider">
                <span>veya</span>
            </div>

            <div class="form-footer">
                <p>Zaten hesabınız var mı? <a href="{{ route('login') }}" class="text-gradient">Giriş Yapın</a></p>
            </div>

            <div class="seller-cta">
                <i class="fas fa-store"></i>
                <p><strong>Satıcı olmak mı istiyorsunuz?</strong></p>
                <p>Önce alıcı olarak kayıt olun, ardından <a href="{{ route('seller.become') }}">Satıcı Ol</a> sayfasından başvürünüzu yapın.</p>
            </div>
        </div>
    </div>
</div>

<!-- KVKK Modal -->
<div id="kvkk-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="hideModal('kvkk-modal')">&times;</span>
        <h2>KVKK Aydınlatma Metni</h2>
        <div class="modal-body">
            <p><strong>{{ config('app.name') }} E-Ticaret Platformu Kişisel Verilerin Korunması ve İşlenmesi Hakkında Aydınlatma Metni</strong></p>

            <p>6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") kapsamında, veri sorumlusu sıfatıyla {{ config('app.name') }} olarak, kişisel verilerinizin güvenliği hususunda azami hassasiyet göstermekteyiz. Bu bilinçle, platformumuz üzerinden toplanan kişisel verileriniz aşağıda açıklanan kapsamda işlenmektedir.</p>

            <h3>1. Veri Sorumlusunun Kimliği</h3>
            <p>{{ config('app.name') }} E-Ticaret Platformu olarak, KVKK kapsamında "Veri Sorumlusu" sıfatıyla hareket etmekteyiz. Platformumuz, İstanbul, Türkiye merkezli olup, iletişim bilgilerimize web sitemiz üzerinden ulaşabilirsiniz.</p>

            <h3>2. Kişisel Verilerin Toplanma Yöntemi ve Hukuki Sebebi</h3>
            <p>Kişisel verileriniz, platformumuz üzerinden gerçekleştirdiğiniz kayıt işlemleri, satın alma işlemleri, mesajlaşma faaliyetleri ve diğer etkileşimler sırasında elektronik ortamda toplanmaktadır. Bu veriler, KVKK'nın 5. ve 6. maddelerinde belirtilen;</p>
            <ul>
                <li>Açık rızanızın bulunması,</li>
                <li>Bir sözleşmenin kurulması veya ifasıyla doğrudan doğruya ilgili olması,</li>
                <li>Veri sorumlusunun hukuki yükümlülüğünü yerine getirebilmesi için zorunlu olması,</li>
                <li>İlgili kişinin temel hak ve özgürlüklerine zarar vermemek kaydıyla, veri sorumlusunun meşru menfaatleri için veri işlenmesinin zorunlu olması</li>
            </ul>
            <p>hukuki sebeplerine dayanarak işlenmektedir.</p>

            <h3>3. İşlenen Kişisel Veriler</h3>
            <p>Platformumuz tarafından aşağıdaki kategorilerdeki kişisel verileriniz işlenmektedir:</p>
            <ul>
                <li><strong>Kimlik Bilgileri:</strong> Ad, soyad</li>
                <li><strong>İletişim Bilgileri:</strong> E-posta adresi, telefon numarası, teslimat adresi</li>
                <li><strong>Hesap Bilgileri:</strong> Kullanıcı adı, şifre (şifrelenmiş halde), profil fotoğrafı</li>
                <li><strong>İşlem Bilgileri:</strong> Satın alma geçmişi, favori ürünler, mesajlaşma kayıtları</li>
                <li><strong>Finansal Bilgiler:</strong> Ödeme bilgileri, fatura adresi</li>
                <li><strong>Dijital İzler:</strong> IP adresi, çerez verileri, tarayıcı bilgileri, giriş/çıkış kayıtları</li>
            </ul>

            <h3>4. Kişisel Verilerin İşlenme Amaçları</h3>
            <p>Toplanan kişisel verileriniz aşağıdaki amaçlarla işlenmektedir:</p>
            <ul>
                <li>Üyelik işlemlerinin gerçekleştirilmesi ve hesap yönetimi</li>
                <li>Satış ve satın alma işlemlerinin yürütülmesi</li>
                <li>Ürün teslimat süreçlerinin yönetilmesi</li>
                <li>Kullanıcılar arası mesajlaşma hizmetinin sağlanması</li>
                <li>Müşteri hizmetleri ve destek taleplerinin karşılanması</li>
                <li>Yasal yükümlülüklerin yerine getirilmesi</li>
                <li>Platform güvenliğinin sağlanması ve dolandırıcılığın önlenmesi</li>
                <li>Kullanıcı deneyiminin iyileştirilmesi</li>
                <li>İstatistiksel analizlerin yapılması</li>
            </ul>

            <h3>5. Kişisel Verilerin Aktarılması</h3>
            <p>Kişisel verileriniz, yukarıda belirtilen amaçların gerçekleştirilmesi doğrultusunda;</p>
            <ul>
                <li>Kargo ve lojistik firmalarına (teslimat işlemleri için)</li>
                <li>Ödeme kuruluşlarına ve bankalara (ödeme işlemleri için)</li>
                <li>Yasal zorunluluk halinde yetkili kamu kurum ve kuruluşlarına</li>
                <li>Hukuki uyuşmazlıklarda avukatlar ve mahkemelere</li>
            </ul>
            <p>KVKK'nın 8. ve 9. maddelerinde belirtilen kişisel veri işleme şartları ve amaçları çerçevesinde aktarılabilecektir.</p>

            <h3>6. Kişisel Verilerin Saklanma Süresi</h3>
            <p>Kişisel verileriniz, işleme amaçlarının gerektirdiği süre boyunca ve yasal zorunluluklar çerçevesinde saklanmaktadır. Üyelik sona erdikten sonra, yasal saklama yükümlülüklerimiz kapsamında gerekli süreler boyunca verileriniz muhafaza edilecek, bu sürelerin bitiminde ise silinecek, yok edilecek veya anonim hale getirilecektir.</p>

            <h3>7. Veri Güvenliği</h3>
            <p>Kişisel verilerinizin güvenliğini sağlamak amacıyla;</p>
            <ul>
                <li>SSL şifreleme teknolojisi kullanılmaktadır</li>
                <li>Şifreler hash algoritmaları ile korunmaktadır</li>
                <li>Düzenli güvenlik güncellemeleri yapılmaktadır</li>
                <li>Erişim kontrolleri uygulanmaktadır</li>
                <li>Veri ihlali durumunda bildirim prosedürleri mevcuttur</li>
            </ul>

            <h3>8. KVKK Kapsamındaki Haklarınız</h3>
            <p>KVKK'nın 11. maddesi uyarınca, veri sorumlusuna başvurarak aşağıdaki haklarınızı kullanabilirsiniz:</p>
            <ul>
                <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                <li>Kişisel verileriniz işlenmişse buna ilişkin bilgi talep etme</li>
                <li>Kişisel verilerin işlenme amacını ve bunların amacına uygun kullanılıp kullanılmadığını öğrenme</li>
                <li>Yurt içinde veya yurt dışında kişisel verilerin aktarıldığı üçüncü kişileri bilme</li>
                <li>Kişisel verilerin eksik veya yanlış işlenmiş olması halinde bunların düzeltilmesini isteme</li>
                <li>KVKK'nın 7. maddesinde öngörülen şartlar çerçevesinde kişisel verilerin silinmesini veya yok edilmesini isteme</li>
                <li>Düzeltme, silme veya yok etme işlemlerinin, kişisel verilerin aktarıldığı üçüncü kişilere bildirilmesini isteme</li>
                <li>İşlenen verilerin münhasıran otomatik sistemler vasıtasıyla analiz edilmesi suretiyle aleyhinize bir sonucun ortaya çıkmasına itiraz etme</li>
                <li>Kişisel verilerinizin kanuna aykırı olarak işlenmesi sebebiyle zarara uğramanız halinde zararın giderilmesini talep etme</li>
            </ul>

            <h3>9. Başvuru Yöntemi</h3>
            <p>Yukarıda belirtilen haklarınızı kullanmak için, kimliğinizi tespit edici gerekli bilgiler ile KVKK'nın 11. maddesinde belirtilen haklardan kullanmayı talep ettiğiniz hakkınıza yönelik açıklamalarınızı içeren talebinizi; "emirhanbilisim52@gmail.com" adresine e-posta yoluyla iletebilirsiniz.</p>

            <p><em>Bu aydınlatma metni, {{ date('d.m.Y') }} tarihinde güncellenmiştir.</em></p>
        </div>
    </div>
</div>

<!-- Kullanım Şartları Modal -->
<div id="kullanim-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="hideModal('kullanim-modal')">&times;</span>
        <h2>Kullanım Şartları ve Koşulları</h2>
        <div class="modal-body">
            <p><strong>{{ config('app.name') }} E-Ticaret Platformu Kullanım Şartları ve Koşulları</strong></p>

            <p>Bu kullanım şartları, {{ config('app.name') }} e-ticaret platformunu ("Platform") kullanan tüm kullanıcılar ("Kullanıcı") için bağlayıcıdır. Platformu kullanarak bu şartları kabul etmiş sayılırsınız.</p>

            <h3>1. Tanımlar</h3>
            <ul>
                <li><strong>Platform:</strong> {{ config('app.name') }} web sitesi ve mobil uygulamaları</li>
                <li><strong>Alıcı:</strong> Platform üzerinden ürün satın alan kullanıcı</li>
                <li><strong>Satıcı:</strong> Platform üzerinden ürün satan kullanıcı</li>
                <li><strong>Hizmet:</strong> Platform tarafından sunulan tüm özellikler ve işlevler</li>
            </ul>

            <h3>2. Üyelik Koşulları</h3>
            <ul>
                <li>Üyelik için 18 yaşını doldurmuş olmak veya yasal vasi onayına sahip olmak gerekir.</li>
                <li>Kayıt sırasında doğru, güncel ve eksiksiz bilgiler verilmelidir.</li>
                <li>Her kullanıcı yalnızca bir hesap açabilir.</li>
                <li>Hesap bilgilerinin (şifre dahil) güvenliği tamamen kullanıcının sorumluluğundadır.</li>
                <li>Hesabınızda yetkisiz erişim tespit ederseniz derhal bize bildirmelisiniz.</li>
                <li>Platform, herhangi bir gerekçe göstermeksizin üyelik başvurusunu reddetme hakkını saklı tutar.</li>
            </ul>

            <h3>3. Satıcı Yükümlülükleri</h3>
            <ul>
                <li>Satıcılar, ilan ettikleri ürünlerin yasal olmasından sorumludur.</li>
                <li>Ürün açıklamaları doğru ve eksiksiz olmalıdır.</li>
                <li>Ürün görselleri gerçeği yansıtmalıdır.</li>
                <li>Satıcılar, alıcılarla yapılan anlaşmalara uymakla yükümlüdür.</li>
                <li>Yasadışı, tehlikeli veya telif hakkı ihlali içeren ürünlerin satışı kesinlikle yasaktır.</li>
                <li>Satıcılar, vergi ve yasal yükümlülüklerinden kendileri sorumludur.</li>
            </ul>

            <h3>4. Alıcı Yükümlülükleri</h3>
            <ul>
                <li>Alıcılar, satın alma öncesi ürün bilgilerini dikkatlice incelemelidir.</li>
                <li>Ödeme işlemlerinde doğru bilgiler kullanılmalıdır.</li>
                <li>Teslimat adresi eksiksiz ve doğru olarak belirtilmelidir.</li>
                <li>Ürün teslim alındığında kontrol edilmeli, hasarlı ürünler bildirilmelidir.</li>
            </ul>

            <h3>5. Yasaklı Ürün ve Hizmetler</h3>
            <p>Aşağıdaki ürün ve hizmetlerin platformda satışı kesinlikle yasaktır:</p>
            <ul>
                <li>Yasadışı maddeler ve uyuşturucular</li>
                <li>Silahlar ve patlayıcılar</li>
                <li>Sahte ve taklit ürünler</li>
                <li>Çalıntı mallar</li>
                <li>Telif hakkı ihlali içeren ürünler</li>
                <li>Sağlığa zararlı veya onaysız ilaçlar</li>
                <li>Canlı hayvanlar (yasalarla belirlenen istisnalar hariç)</li>
                <li>Müstehcen içerikler</li>
            </ul>

            <h3>6. Ödeme ve Teslimat</h3>
            <ul>
                <li>Ödeme işlemleri, satıcı ve alıcı arasında doğrudan gerçekleştirilir.</li>
                <li>Platform, ödeme aracılığı hizmeti sunmamaktadır.</li>
                <li>Teslimat koşulları ve süreleri satıcı tarafından belirlenir.</li>
                <li>Kargo ücretleri ayrıca belirtilmediği sürece alıcıya aittir.</li>
            </ul>

            <h3>7. İade ve Cayma Hakkı</h3>
            <ul>
                <li>Tüketiciler, 6502 sayılı Tüketicinin Korunması Hakkında Kanun kapsamında cayma hakkına sahiptir.</li>
                <li>Cayma hakkı, ürünün teslim alındığı tarihten itibaren 14 gün içinde kullanılabilir.</li>
                <li>Cayma hakkını kullanmak için ürün, orijinal ambalajında ve kullanılmamış olmalıdır.</li>
                <li>Bazı ürün kategorileri cayma hakkı kapsamı dışındadır (kişiye özel üretilen ürünler, çabuk bozulan ürünler vb.).</li>
                <li>İade kargo ücreti, aksi belirtilmedikçe alıcıya aittir.</li>
            </ul>

            <h3>8. Fikri Mülkiyet Hakları</h3>
            <ul>
                <li>Platform üzerindeki tüm içerik, tasarım ve yazılımlar {{ config('app.name') }}'a aittir.</li>
                <li>Kullanıcılar, yükledikleri içeriklerin telif haklarına sahip olduklarını beyan eder.</li>
                <li>Fikri mülkiyet ihlali tespit edilen ilanlar derhal kaldırılır.</li>
            </ul>

            <h3>9. Sorumluluk Sınırları</h3>
            <ul>
                <li>Platform, satıcı ve alıcı arasındaki işlemlerde sadece aracı konumundadır.</li>
                <li>Satıcıların sundukları ürün ve hizmetlerin kalitesinden platform sorumlu değildir.</li>
                <li>Platform, kullanıcılar arası anlaşmazlıklarda taraf değildir.</li>
                <li>Teknik aksaklıklar nedeniyle oluşabilecek zararlardan platform sorumlu tutulamaz.</li>
            </ul>

            <h3>10. Hesap Askıya Alma ve Fesih</h3>
            <ul>
                <li>Kullanım şartlarını ihlal eden hesaplar uyarılmadan askıya alınabilir.</li>
                <li>Tekrarlayan ihlallerde hesap kalıcı olarak kapatılabilir.</li>
                <li>Kullanıcılar, istedikleri zaman hesaplarını kapatabilir.</li>
            </ul>

            <h3>11. Uyuşmazlık Çözümü</h3>
            <p>Bu şartlardan doğacak uyuşmazlıklarda Türkiye Cumhuriyeti kanunları uygulanır. İstanbul Mahkemeleri ve İcra Daireleri yetkilidir.</p>

            <h3>12. Değişiklikler</h3>
            <p>{{ config('app.name') }}, bu kullanım şartlarını herhangi bir zamanda değiştirme hakkını saklı tutar. Değişiklikler, platformda yayınlandığı andan itibaren geçerli olur.</p>

            <p><em>Bu kullanım şartları, {{ date('d.m.Y') }} tarihinde güncellenmiştir.</em></p>
        </div>
    </div>
</div>

<script>
function showModal(id) {
    document.getElementById(id).style.display = 'block';
    document.body.style.overflow = 'hidden';
}
function hideModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = 'auto';
}
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}
</script>
@endsection
