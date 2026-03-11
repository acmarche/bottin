
Gestion de l'annuaire marchois

Commerces, santé, sport, etc.

```bash                                                                                                                                                                                               
php artisan bottin:meili --flush  
```

```bash                  
curl -X PUT http://localhost:7700/indexes/bottin_laravel_shops_index/settings/filterable-attributes \
  -H "Authorization: Bearer YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '["city", "tags", "type", "_geo"]'
```
         
```bash                                                                                                                                                                                                                                      
curl -X POST https://YOUR-PROD-DOMAIN/api/bottin/map/update \                                                                                                                                                                    
  -H "Content-Type: application/json" \                                                                                                                                                                                          
  -H "Accept: application/json" \                                                                                                                                                                                                
  -d '{                                                                                                                                                                                                                       
    "localite": "Marche-en-Famenne",                                                                                                                                                                                             
    "tags": ["restaurant"],
    "coordinates": {                                                                                                                                                                                                             
      "latitude": 50.2268,                                                                                                                                                                                                       
      "longitude": 5.3442                                                                                                                                                                                                        
    }                                                                                                                                                                                                                            
  }'
```

Or with no params to get all results:

```bash   
curl -X POST https://YOUR-PROD-DOMAIN/api/bottin/map/update \
   -H "Content-Type: application/json" \
   -H "Accept: application/json"
```
