<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    public function index()
    {
        $exercises = Exercise::all();
        return view('exercises.index', compact('exercises'));
    }

    public function create()
    {
        return view('exercises.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3'
        ]);

        Exercise::create([
    'name'         => $request->name,
    'description'  => $request->description,
    'muscle_group' => $request->muscle_group,
    'image_url'    => $request->image_url ?? null,
]);

        return redirect()->route('exercises.index');
    }

    public function edit($id)
    {
        $exercise = Exercise::findOrFail($id);
        return view('exercises.edit', compact('exercise'));
    }

    public function update(Request $request, $id)
    {
        $exercise = Exercise::findOrFail($id);

        $exercise->update([
    'name'         => $request->name,
    'description'  => $request->description,
    'muscle_group' => $request->muscle_group,
    'image_url'    => $request->image_url ?? $exercise->image_url,
]);

        return redirect()->route('exercises.index');
    }

    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->delete();

        return back()->with('success', 'Exercício removido com sucesso.');
    }

    private array $exerciseDictionary = [
        // Peito
        'supino'                   => 'bench press',   // ← adicionado
        'supino reto'              => 'bench press',
        'supino inclinado'         => 'incline bench press',
        'supino declinado'         => 'decline bench press',
        'supino com halteres'      => 'dumbbell bench press',
        'crucifixo'                => 'dumbbell fly',
        'crucifixo inclinado'      => 'incline dumbbell fly',
        'crossover'                => 'cable crossover',
        'peck deck'                => 'pec deck',
        'flexão de braço'          => 'push up',
        'flexão'                   => 'push up',

        // Costas
        'puxada frontal'           => 'lat pulldown',
        'puxada'                   => 'lat pulldown',
        'remada curvada'           => 'bent over row',
        'remada baixa'             => 'seated cable row',
        'remada unilateral'        => 'one arm dumbbell row',
        'remada'                   => 'barbell row',
        'pulldown'                 => 'lat pulldown',
        'levantamento terra'       => 'deadlift',
        'terra'                    => 'deadlift',
        'barra fixa'               => 'pull up',
        'pull up'                  => 'pull up',
        'chin up'                  => 'chin up',
        'hiperextensão'            => 'back extension',

        // Ombros
        'desenvolvimento'          => 'shoulder press',
        'desenvolvimento com barra'=> 'overhead press',
        'desenvolvimento militar'  => 'military press',
        'elevação lateral'         => 'lateral raise',
        'elevação frontal'         => 'front raise',
        'elevação posterior'       => 'rear delt fly',
        'encolhimento'             => 'shrug',
        'arnold press'             => 'arnold press',
        'face pull'                => 'face pull',

        // Bíceps
        'rosca direta'             => 'barbell curl',
        'rosca alternada'          => 'alternating dumbbell curl',
        'rosca martelo'            => 'hammer curl',
        'rosca scott'              => 'preacher curl',
        'rosca concentrada'        => 'concentration curl',
        'rosca no cabo'            => 'cable curl',
        'rosca'                    => 'bicep curl',
        'bicep curl'               => 'bicep curl',

        // Tríceps
        'tríceps testa'            => 'skull crusher',
        'triceps testa'            => 'skull crusher',
        'tríceps corda'            => 'tricep pushdown',
        'triceps corda'            => 'tricep pushdown',
        'tríceps francês'          => 'french press',
        'triceps francês'          => 'french press',
        'tríceps mergulho'         => 'tricep dip',
        'triceps mergulho'         => 'tricep dip',
        'tríceps'                  => 'tricep extension',
        'triceps'                  => 'tricep extension',
        'extensão de tríceps'      => 'tricep extension',
        'dip'                      => 'dip',

        // Pernas
        'agachamento'              => 'squat',
        'agachamento livre'        => 'barbell squat',
        'agachamento hack'         => 'hack squat',
        'agachamento sumô'         => 'sumo squat',
        'leg press'                => 'leg press',
        'extensão de pernas'       => 'leg extension',
        'flexão de pernas'         => 'leg curl',
        'mesa flexora'             => 'leg curl',
        'cadeira extensora'        => 'leg extension',
        'avanço'                   => 'lunge',
        'afundo'                   => 'lunge',
        'stiff'                    => 'romanian deadlift',
        'levantamento terra romeno' => 'romanian deadlift',
        'panturrilha'              => 'calf raise',
        'elevação de panturrilha'  => 'calf raise',
        'abdutor'                  => 'hip abduction',
        'adutor'                   => 'hip adduction',
        'glúteo'                   => 'glute bridge',
        'cadeira abdutora'         => 'hip abduction machine',

        // Abdômen
        'abdominal'                => 'crunch',
        'crunch'                   => 'crunch',
        'abdominal infra'          => 'reverse crunch',
        'prancha'                  => 'plank',
        'elevação de pernas'       => 'leg raise',
        'russian twist'            => 'russian twist',
        'abdominal oblíquo'        => 'oblique crunch',

        // Cardio / Funcional
        'esteira'                  => 'treadmill',
        'bicicleta'                => 'stationary bike',
        'elíptico'                 => 'elliptical',
        'burpee'                   => 'burpee',
        'polichinelo'              => 'jumping jacks',
        'pular corda'              => 'jump rope',
    ];

    private function translateToEnglish(string $term): string
    {
        $lower = mb_strtolower(trim($term));

        // Busca exata primeiro
        if (isset($this->exerciseDictionary[$lower])) {
            return $this->exerciseDictionary[$lower];
        }

        // Busca parcial — chaves mais longas têm prioridade (mais específicas)
        $best = null;
        $bestLen = 0;
        foreach ($this->exerciseDictionary as $pt => $en) {
            if (str_contains($lower, $pt) || str_contains($pt, $lower)) {
                if (strlen($pt) > $bestLen) {
                    $best    = $en;
                    $bestLen = strlen($pt);
                }
            }
        }

        return $best ?? $term;
    }

    public function searchImages(Request $request)
    {
        $query = trim($request->q ?? '');

        if (strlen($query) < 3) {
            return response()->json(['images' => [], 'video_url' => null]);
        }

        $searchTerm = $this->translateToEnglish($query);
        $images = [];
        $apiQuery = trim($searchTerm . ' exercise');

        try {
            
            $pexelsKey = config('services.pexels.key');
            if ($pexelsKey) {
                $pexelsUrl = "https://api.pexels.com/v1/search?query=" . urlencode($apiQuery) . "&per_page=12&orientation=landscape";

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $pexelsKey
                ])->timeout(10)->get($pexelsUrl);

                if ($response->successful()) {
                    $data = $response->json();
                    $photos = $data['photos'] ?? [];

                    foreach ($photos as $photo) {
                        if (count($images) >= 8) break;

                        $images[] = [
                            'url' => $photo['src']['medium'] ?? $photo['src']['large'],
                            'exercise_id' => null,
                            'type' => 'image'
                        ];
                    }
                }
            }

            
            if (empty($images)) {
                $pixabayKey = config('services.pixabay.key');
                if ($pixabayKey) {
                    $pixabayUrl = "https://pixabay.com/api/?key={$pixabayKey}&q=" . urlencode($apiQuery) . "&image_type=photo&per_page=12&safesearch=true";

                    $response = \Illuminate\Support\Facades\Http::timeout(10)->get($pixabayUrl);

                    if ($response->successful()) {
                        $data = $response->json();
                        $hits = $data['hits'] ?? [];

                        foreach ($hits as $hit) {
                            if (count($images) >= 8) break;

                            $images[] = [
                                'url' => $hit['webformatURL'] ?? $hit['previewURL'],
                                'exercise_id' => null,
                                'type' => 'image'
                            ];
                        }
                    }
                }
            }

            // Fallback sempre disponível - funciona sem chaves de API
            if (empty($images)) {
                $fallbackQueries = [
                    $apiQuery,
                    $searchTerm . ' fitness',
                    $query . ' gym exercise',
                ];

                $fallbackUrls = [
                    'https://source.unsplash.com/featured/400x300/?' . urlencode($fallbackQueries[0]),
                    'https://source.unsplash.com/random/400x300/?' . urlencode($fallbackQueries[1]),
                    'https://loremflickr.com/400/300/' . urlencode($fallbackQueries[2]),
                    'https://picsum.photos/400/300?random=' . mt_rand(),
                ];

                foreach ($fallbackUrls as $url) {
                    $images[] = [
                        'url' => $url,
                        'exercise_id' => null,
                        'type' => 'image'
                    ];
                    if (count($images) >= 6) break;
                }
            }

            return response()->json([
                'images' => array_values($images),
                'video_url' => null,
                'searched_as' => $searchTerm,
            ])->header('Access-Control-Allow-Origin', '*')
              ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
              ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        } catch (\Exception $e) {
            // Fallback de emergência - sempre funciona
            $images = [
                [
                    'url' => 'https://picsum.photos/400/300?random=' . mt_rand(),
                    'exercise_id' => null,
                    'type' => 'image'
                ],
                [
                    'url' => 'https://source.unsplash.com/random/400x300/?' . urlencode($query),
                    'exercise_id' => null,
                    'type' => 'image'
                ]
            ];

            return response()->json([
                'images' => $images,
                'video_url' => null,
                'error' => $e->getMessage()
            ])->header('Access-Control-Allow-Origin', '*')
              ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
              ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        }
    }
}
