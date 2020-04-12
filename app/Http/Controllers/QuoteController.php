<?php

namespace App\Http\Controllers;

use App\Repositories\FileRepository;
use App\Repositories\QuoteRepository;
use Illuminate\Http\Request;

class QuoteController extends Controller
{

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var FileRepository
     */
    protected $fileRepository;

    /**
     * QuoteController constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param FileRepository $fileRepository
     */
    public function __construct(QuoteRepository $quoteRepository, FileRepository $fileRepository)
    {
        $this->quoteRepository = $quoteRepository;
        $this->fileRepository = $fileRepository;
    }

    public function calculate($from, $to)
    {
        $routeCalculated = $this->quoteRepository->calculate($from, $to);
        return response()->json($routeCalculated);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'from' => 'required',
            'to' => 'required',
            'price' => 'required|numeric|gt:0'
        ], [
            'from.required' => 'O campo from precisa ser enviado',
            'to.required' => 'O to from precisa ser enviado',
            'price.required' => 'O price from precisa ser enviado',
            'price.numeric' => 'Digite um valor',
            'price.gt' => 'Digite um valor acima de 0',
        ]);

        $requestBody = $request->json();
        $saved = $this->fileRepository->save($requestBody->all());
        return response()->json($requestBody->all());
    }

}
