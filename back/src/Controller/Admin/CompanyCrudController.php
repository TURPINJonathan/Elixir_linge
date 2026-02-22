<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CompanyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Company::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Entreprise')
            ->setEntityLabelInPlural('Entreprises')
            ->setPageTitle(Crud::PAGE_INDEX, 'Entreprises')
            ->setDefaultSort(['created_at' => 'DESC'])
            ->setSearchFields(['name', 'city', 'email', 'phone_number']);
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id')->hideOnForm()->hideOnIndex()->hideOnDetail(),
                TextField::new('name', 'Nom'),
                TextField::new('address', 'Adresse'),
                TextField::new('city', 'Ville'),
                EmailField::new('email', 'Email'),
                TelephoneField::new('phoneNumber', 'Téléphone'),
                DateTimeField::new('createdAt', 'Créé le')->setFormat('dd/MM/yyyy')->hideOnIndex(),
            ];
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            return [
                FormField::addPanel('Informations')->setIcon('fa fa-building'),
                TextField::new('name', 'Nom')->setFormTypeOption('attr', ['placeholder' => 'Elixir Linge'])->setColumns(12),

                FormField::addPanel('Adresse')->setIcon('fa fa-map-marker'),
                TextField::new('address', 'Adresse')->setColumns(12),
                TextField::new('postalCode', 'Code postal')->setColumns(3),
                TextField::new('city', 'Ville')->setColumns(9),

                FormField::addPanel('Contact')->setIcon('fa fa-phone'),
                TelephoneField::new('phoneNumber', 'Téléphone')->setColumns(6),
                EmailField::new('email', 'Email')->setColumns(6),

                FormField::addPanel('Notes privées')->setIcon('fa fa-lock'),
                TextareaField::new('privateNote', 'Note')->setFormTypeOption('attr', ['placeholder' => 'La note privée ne sera jamais envoyée à qui que ce soit !'])->setColumns(12),

                FormField::addPanel('Métadonnées')->setIcon('fa fa-clock'),
                DateTimeField::new('createdAt', 'Créé le')->setColumns(6),
                DateTimeField::new('updatedAt', 'Modifié le')->setColumns(6),
            ];
        }

        // PAGE_EDIT / PAGE_NEW
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex()->hideOnDetail(),
            
            FormField::addColumn(8),
            FormField::addPanel('Informations')->setIcon('fa fa-building'),
            TextField::new('name', 'Nom')->setColumns(12)->setFormTypeOption('attr', ['placeholder' => 'Turpinou Society']),

            FormField::addPanel('Adresse')->setIcon('fa fa-map-marker'),
            
            TextField::new('address', 'Rue')
                ->setColumns(12)
                ->setFormTypeOption('attr', ['id' => 'address', 'placeholder' => '37 rue du plus beau']),
            TextField::new('postalCode', 'Code postal')->setColumns(3)
                ->setFormTypeOption('attr', ['id' => 'postal-code', 'placeholder' => '14000']),
            TextField::new('city', 'Ville')->setColumns(9)
                ->setFormTypeOption('attr', ['id' => 'city', 'placeholder' => 'Turpinouville']),
            
            TextField::new('latitude')
                ->setFormTypeOption('attr', ['id' => 'latitude', 'type' => 'hidden'])
                ->setFormTypeOption('row_attr', ['style' => 'display:none']),
            TextField::new('longitude')
                ->setFormTypeOption('attr', ['id' => 'longitude', 'type' => 'hidden'])
                ->setFormTypeOption('row_attr', ['style' => 'display:none']),

            FormField::addPanel('Contact')->setIcon('fa fa-phone'),
            TelephoneField::new('phoneNumber', 'Téléphone')->setFormTypeOption('attr', ['placeholder' => '06 12 34 56 78'])->setColumns(6),
            EmailField::new('email', 'Email')->setFormTypeOption('attr', ['placeholder' => 'turpinou@merveille.com'])->setColumns(6),

            FormField::addPanel('Informations complémentaires')->setIcon('fa fa-plus'),
            TextEditorField::new('privateNote', 'Notes privées')->setFormTypeOption('attr', ['placeholder' => 'La note privée ne sera jamais envoyée à qui que ce soit !'])->setColumns(12),

            FormField::addColumn(4),
            FormField::addPanel('Carte')->setIcon('fa fa-map')
                ->setHelp($this->renderMapWidget()),

            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm(),
        ];
    }

    private function renderMapWidget(): string
    {
        return <<<'HTML'
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<div id="company-map" style="height: 400px; width: 100%; border: 1px solid #ddd; border-radius: 4px; margin-top: 10px;"></div>
<div style="margin-top: 5px; font-size: 12px; color: #666;">
    💡 Astuce : Tu peux déplacer le marqueur sur la carte pour ajuster précisément la position
</div>
<style>
#address-search-results {
    position: absolute;
    z-index: 1000;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 400px;
    overflow-y: auto;
    width: 100%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
#address-search-results .result-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}
#address-search-results .result-item:hover {
    background: #f5f5f5;
}
#address-search-results .result-item .main-text {
    font-weight: 500;
    color: #333;
}
#address-search-results .result-item .score {
    display: inline-block;
    background: #e3f2fd;
    color: #1976d2;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
    margin-left: 5px;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la carte
    const defaultLat = 48.8566;
    const defaultLng = 2.3522;
    
    const map = L.map('company-map').setView([defaultLat, defaultLng], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    let marker = null;
    
    // Fonction pour mettre à jour la carte
    function updateMap(lat, lng, name = '', draggable = false) {
        map.setView([lat, lng], 18); // Zoom 18 pour plus de précision
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lng], { draggable: draggable }).addTo(map);
        if (name) {
            marker.bindPopup(`<b>${name}</b>`).openPopup();
        }
        
        // Si le marqueur est déplaçable, mettre à jour les coordonnées
        if (draggable) {
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                const latInput = document.querySelector('input[name*="latitude"]');
                const lngInput = document.querySelector('input[name*="longitude"]');
                if (latInput && lngInput) {
                    latInput.value = pos.lat.toFixed(7);
                    lngInput.value = pos.lng.toFixed(7);
                }
            });
        }
    }
    
    // Trouver les champs par leur nom (plus robuste que par ID)
    const latInput = document.querySelector('input[name*="latitude"]');
    const lngInput = document.querySelector('input[name*="longitude"]');
    
    // Charger position existante si disponible
    if (latInput && lngInput && latInput.value && lngInput.value) {
        updateMap(parseFloat(latInput.value), parseFloat(lngInput.value), '', true);
    }
    
    // Autocomplétion directement sur le champ adresse
    const searchInput = document.querySelector('input[name*="address"]');
    if (!searchInput) {
        console.warn('Champ adresse introuvable');
        return;
    }
    
    console.log('Champ adresse trouvé:', searchInput);
    
    let resultsDiv = document.createElement('div');
    resultsDiv.id = 'address-search-results';
    resultsDiv.style.display = 'none';
    
    // Trouver le conteneur parent du champ
    const fieldContainer = searchInput.closest('.field-group') || searchInput.closest('.form-group') || searchInput.parentElement;
    fieldContainer.style.position = 'relative';
    fieldContainer.appendChild(resultsDiv);
    
    let debounceTimer;
    
    searchInput.addEventListener('input', function(e) {
        console.log('Input event:', e.target.value);
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();
        
        if (query.length < 3) {
            resultsDiv.style.display = 'none';
            return;
        }
        
        debounceTimer = setTimeout(async () => {
            console.log('Recherche pour:', query);
            try {
                const response = await fetch(
                    `https://data.geopf.fr/geocodage/search?q=${encodeURIComponent(query)}&limit=10`
                );
                const data = await response.json();
                console.log('Résultats:', data);
                
                resultsDiv.innerHTML = '';
                
                if (data.features && data.features.length > 0) {
                    data.features.forEach(feature => {
                        const item = document.createElement('div');
                        item.className = 'result-item';
                        
                        const props = feature.properties;
                        const mainText = document.createElement('div');
                        mainText.className = 'main-text';
                        mainText.textContent = props.label;
                        
                        // Score de confiance
                        if (props.score) {
                            const score = document.createElement('span');
                            score.className = 'score';
                            score.textContent = `${Math.round(props.score * 100)}%`;
                            mainText.appendChild(score);
                        }
                        
                        item.appendChild(mainText);
                        item.dataset.feature = JSON.stringify(feature);
                        
                        item.addEventListener('click', function() {
                            const f = JSON.parse(this.dataset.feature);
                            const props = f.properties;
                            const coords = f.geometry.coordinates;
                            
                            console.log('Adresse sélectionnée:', props);
                            
                            // Chercher les champs par name
                            const postalCodeField = document.querySelector('input[name*="postalCode"]');
                            const cityField = document.querySelector('input[name*="city"]');
                            
                            // Construire l'adresse complète (numéro + rue)
                            const fullAddress = [props.housenumber, props.street]
                                .filter(Boolean)
                                .join(' ');
                            
                            // Remplir les champs
                            searchInput.value = fullAddress || '';
                            if (postalCodeField) postalCodeField.value = props.postcode || '';
                            if (cityField) cityField.value = props.city || '';
                            
                            // Sauvegarder lat/lng (GeoJSON: [lng, lat])
                            if (latInput && lngInput) {
                                latInput.value = coords[1].toFixed(7);
                                lngInput.value = coords[0].toFixed(7);
                            }
                            
                            // Mettre à jour la carte avec marqueur déplaçable
                            updateMap(coords[1], coords[0], props.label, true);
                            
                            // Fermer les résultats
                            resultsDiv.style.display = 'none';
                        });
                        
                        resultsDiv.appendChild(item);
                    });
                    
                    resultsDiv.style.display = 'block';
                } else {
                    resultsDiv.style.display = 'none';
                }
            } catch (error) {
                console.error('Erreur autocomplétion:', error);
            }
        }, 300);
    });
    
    // Fermer les résultats au clic ailleurs
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });
});
</script>
HTML;
    }
}
