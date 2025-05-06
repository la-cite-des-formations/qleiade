import React from "react";
import { Card } from "@mui/material";

export default function MyNodeGrap({ wealths }) {
    // données en entrée:
    // wealths = [
    //     {
    //         wealthId: "1",
    //         wealthName: "toto",
    //         actions: [{id:"1", order:"1", name:"action1"}, {id:"2", order:"2", name:"action2"}],
    //         indicators: ["indicator1", "indicator2"],
    //     },
    //     {
    //         wealthId: "2",
    //         wealthName: "titi",
    //         actions: [{id:"3", order:"3", name:"action3"},{id:"4", order:"4", name:"action4"},{id:"5", order:"5", name:"action5"}],
    //         indicators: ["indicator2", "indicator3"],
    //     },
    //     {
    //         wealthId: "3",
    //         wealthName: "tata",
    //         actions: [{id:"1", order:"1", name:"action1"}, {id:"5", order:"5", name:"action5"}],
    //         indicators: ["indicator1", "indicator3"],
    //     },]

    //je veux construire un graphique en node avec le dataset si dessus en react,
    //faire toutes les fonctions nécessaire pour faciliter la lecture du code
    //toutes les nodes sont des cards
    // toutes les nodes sont répartis sur 6 colonnes
    //les nodes action sont sur la première colonne de gauche
    //les nodes indicateur sont sur la dernière colonne de droite
    //les nodes wealth sont réparti sur les 4 colonnes centrale pour plus de lisibilité, elles sont placé le plus judicieusement possible par rapport aux positions de ces actions et de ces indicateurs
    //créer toutes les nodes action et indicateur du dataset et les stocker dans un tableau de nodes.
    //pour chaque wealth:
    //-créer la node wealth et la positionner comme indiqué plus haut
    //-rechercher la ou les node action dans le tableau de node, créer un lien entre wealth et action trouvé
    //-rechercher la ou les node indicateur dans le tableau de node, créer un lien entre wealth et indicateur trouvé

    // retourner le graph svg ainsi construit

}
