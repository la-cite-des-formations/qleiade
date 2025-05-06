## All you need to play with public interface

## audit schema 

```json
{
    "id": "date + name",
    "name": "nom de l'audit renseigner dans le formulaire d'enregistrement",
    "date": "date de l'audit",
    "auditor": "nom de l'auditeur facultatif",
    "user": "mail de la personne qui valide l'audit",
    "type": "préparation /audit interne / audit extern",
    "comment": "notes durant l'audit",
    "sample": {
        "type": "tyupe d'échantillonnage",
        "formations": [],
        "groups": [],
        "students": [],
        "periods": []
    },
    "validated": "true/false",
    "wealths": [
        {
            "id": "",
            "confirmed": "true/false"
        }
    ]
}

```
