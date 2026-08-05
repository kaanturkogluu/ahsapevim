<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        Page::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->input('content') ?? '',
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Bilgilendirme sayfası başarıyla eklendi.');
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        
        $contactData = null;
        if ($page->slug === 'iletisim') {
            $contactData = json_decode($page->content, true);
            if (!is_array($contactData)) {
                // Fallback default values if content is raw HTML
                $contactData = [
                    'phone' => '0850 XXX XX XX',
                    'whatsapp' => '05XX XXX XX XX',
                    'working_hours_weekdays' => '09:00 - 18:00',
                    'working_hours_saturday' => '10:00 - 15:00',
                    'address' => "Şehzadeler Mevkii, Merkez\nManisa, Türkiye",
                    'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d100001.32837311103!2d27.359288219030202!3d38.61867137839352!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b98d249f0322b7%3A0xc486be78a2e7c4f4!2sManisa%2C%20%C5%9Eehzadeler%2FManisa!5e0!3m2!1str!2str!4v1700000000000!5m2!1str!2str',
                    'email' => 'info@ahsapevim.com',
                    'note' => '',
                ];
            }
        }

        $faqItems = null;
        if ($page->slug === 'sikca-sorulanlar') {
            $faqItems = json_decode($page->content, true);
            if (!is_array($faqItems)) {
                $faqItems = [
                    [
                        'question' => 'Siparişim kaç günde ulaşır?',
                        'answer' => 'Siparişleriniz ortalama 1-3 iş günü içinde kargoya teslim edilmektedir.'
                    ],
                    [
                        'question' => 'İade koşulları nelerdir?',
                        'answer' => 'Kişiselleştirilmiş ürünler hariç 14 gün içinde iade hakkınız bulunmaktadır.'
                    ],
                    [
                        'question' => 'Ürünleriniz masif ahşap mı?',
                        'answer' => 'Evet, tüm ürünlerimiz birinci sınıf masif ağaç kullanılarak Manisa atölyemizde el işçiliği ile üretilmektedir.'
                    ]
                ];
            }
        }

        return view('admin.pages.edit', compact('page', 'contactData', 'faqItems'));
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        if ($page->slug === 'sikca-sorulanlar') {
            $request->validate([
                'title' => 'required|string|max:255',
                'faqs' => 'nullable|array',
                'faqs.*.question' => 'nullable|string|max:500',
                'faqs.*.answer' => 'nullable|string',
            ]);

            $rawFaqs = $request->input('faqs', []);
            $faqItems = [];

            foreach ($rawFaqs as $item) {
                $q = trim($item['question'] ?? '');
                $a = trim($item['answer'] ?? '');
                if (!empty($q) || !empty($a)) {
                    $faqItems[] = [
                        'question' => $q,
                        'answer' => $a,
                    ];
                }
            }

            $page->update([
                'title' => $request->title,
                'content' => json_encode($faqItems, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.pages.index')->with('success', 'Sıkça Sorulan Sorular başarıyla güncellendi.');
        }

        if ($page->slug === 'iletisim') {
            $request->validate([
                'title' => 'required|string|max:255',
                'phone' => 'nullable|string|max:255',
                'whatsapp' => 'nullable|string|max:255',
                'working_hours_weekdays' => 'nullable|string|max:255',
                'working_hours_saturday' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'map_url' => 'nullable|string',
                'email' => 'nullable|string|max:255',
                'note' => 'nullable|string',
            ]);

            $contactData = [
                'phone' => $request->input('phone', ''),
                'whatsapp' => $request->input('whatsapp', ''),
                'working_hours_weekdays' => $request->input('working_hours_weekdays', ''),
                'working_hours_saturday' => $request->input('working_hours_saturday', ''),
                'address' => $request->input('address', ''),
                'map_url' => $request->input('map_url', ''),
                'email' => $request->input('email', ''),
                'note' => $request->input('note', ''),
            ];

            $page->update([
                'title' => $request->title,
                'content' => json_encode($contactData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('admin.pages.index')->with('success', 'İletişim bilgileri başarıyla güncellendi.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $id,
            'content' => 'nullable|string',
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'content' => $request->input('content') ?? '',
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Bilgilendirme sayfası başarıyla güncellendi.');
    }

    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Bilgilendirme sayfası silindi.');
    }
}
