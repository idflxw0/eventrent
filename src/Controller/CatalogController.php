<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Repository\CategoryRepository;
use App\Repository\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogController extends AbstractController
{
    private const PARAM_CAT      = 'cat';
    private const PARAM_DISPO    = 'dispo';
    private const PARAM_PRIX_MAX = 'prix_max';
    private const PARAM_SORT     = 'tri';
    private const PARAM_PAGE     = 'page';
    private const PRICE_RANGE_MAX = 500;

    #[Route('/catalogue', name: 'catalog_index')]
    public function index(Request $request, EquipmentRepository $repo, CategoryRepository $catRepo): Response
    {
        $categoryId   = $request->query->getInt(self::PARAM_CAT) ?: null;
        $availability = $request->query->get(self::PARAM_DISPO);
        $priceMax     = $request->query->get(self::PARAM_PRIX_MAX)
            ? (float) $request->query->get(self::PARAM_PRIX_MAX) : null;
        $sort         = $request->query->get(self::PARAM_SORT, EquipmentRepository::SORT_NAME);
        $page         = max(1, $request->query->getInt(self::PARAM_PAGE, 1));

        $paginator = $repo->search($categoryId, $availability, $priceMax, $sort, $page);
        $total     = $paginator->count();
        $pages     = (int) ceil($total / EquipmentRepository::DEFAULT_LIMIT);

        $categories     = $catRepo->findAll();
        $activeCategory = $categoryId ? $catRepo->findWithSuppliers($categoryId) : null;
        $categoryCounts = $repo->countByCategory();
        $countMap = [];
        foreach ($categoryCounts as $row) {
            $countMap[(int) $row['category_id']] = $row['cnt'];
        }

        $avgRatings = [];
        foreach ($paginator as $equip) {
            $r = $equip->getReviews();
            $avgRatings[$equip->getId()] = !$r->isEmpty()
                ? round(array_sum(array_map(fn($rv) => $rv->getRating(), $r->toArray())) / $r->count(), 1)
                : null;
        }

        return $this->render('catalog/index.html.twig', [
            'equipment'   => $paginator,
            'total'       => $total,
            'page'        => $page,
            'pages'       => $pages,
            'categories'     => $categories,
            'activeCategory' => $activeCategory,
            'countMap'    => $countMap,
            'avgRatings'  => $avgRatings,
            'filter'      => [
                'catId'   => $categoryId,
                'dispo'   => $availability,
                'prixMax' => $priceMax,
                'sort'     => $sort,
            ],
            'params' => [
                'cat'      => self::PARAM_CAT,
                'dispo'    => self::PARAM_DISPO,
                'prix_max' => self::PARAM_PRIX_MAX,
                'sort'     => self::PARAM_SORT,
                'page'     => self::PARAM_PAGE,
            ],
            'sortOptions' => [
                EquipmentRepository::SORT_NAME      => 'Nom',
                EquipmentRepository::SORT_PRICE     => 'Prix croissant',
                EquipmentRepository::SORT_PRICE_DESC => 'Prix décroissant',
                EquipmentRepository::SORT_NEWEST    => 'Plus récents',
            ],
            'priceRangeMax' => self::PRICE_RANGE_MAX,
            'EqStatus' => [
                'AVAILABLE'    => Equipment::STATUS_AVAILABLE,
                'MAINTENANCE'  => Equipment::STATUS_MAINTENANCE,
                'OUT_OF_SERVICE' => Equipment::STATUS_OUT_OF_SERVICE,
            ],
            'EqType' => [
                'AUDIO' => Equipment::TYPE_AUDIO,
                'VIDEO' => Equipment::TYPE_VIDEO,
            ],
        ]);
    }

    #[Route('/catalogue/{id}', name: 'catalog_show', requirements: ['id' => '\d+'])]
    public function show(int $id, EquipmentRepository $repo): Response
    {
        $equipment = $repo->findOneWithRelations($id);

        if (!$equipment) {
            throw $this->createNotFoundException('Équipement introuvable.');
        }

        $reviews = $equipment->getReviews();
        $avgRating = 0;
        if (!$reviews->isEmpty()) {
            $sum = array_reduce($reviews->toArray(), fn($carry, $r) => $carry + $r->getRating(), 0);
            $avgRating = round($sum / $reviews->count(), 1);
        }

        return $this->render('catalog/show.html.twig', [
            'equipment'   => $equipment,
            'avgRating'   => $avgRating,
            'reviewCount' => $reviews->count(),
            'EqStatus' => [
                'AVAILABLE'    => Equipment::STATUS_AVAILABLE,
                'MAINTENANCE'  => Equipment::STATUS_MAINTENANCE,
                'OUT_OF_SERVICE' => Equipment::STATUS_OUT_OF_SERVICE,
            ],
            'EqType' => [
                'AUDIO' => Equipment::TYPE_AUDIO,
                'VIDEO' => Equipment::TYPE_VIDEO,
            ],
        ]);
    }
}
