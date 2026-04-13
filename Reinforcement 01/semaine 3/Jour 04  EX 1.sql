WITH MoyennesModules AS (
    SELECT 
        a.id AS apprenant_id,
        a.nom AS apprenant_nom,
        p.nom AS promotion_nom,
        m.id AS module_id,
        m.coefficient,
        AVG(n.note) AS moyenne_module
    FROM apprenants a
    JOIN promotions p ON a.promotion_id = p.id
    JOIN notes n ON a.id = n.apprenant_id
    JOIN modules m ON n.module_id = m.id
    WHERE a.statut = 'actif' AND a.promotion_id = 1
    GROUP BY a.id, a.nom, p.nom, m.id, m.coefficient
)
SELECT 
    apprenant_nom AS nom,
    promotion_nom AS promotion,
    SUM(moyenne_module * coefficient) / SUM(coefficient) AS moyenne_ponderee
FROM MoyennesModules
GROUP BY apprenant_id, apprenant_nom, promotion_nom
ORDER BY moyenne_ponderee DESC;


SELECT 
    m.nom,
    AVG(n.note) AS note_moyenne,
    MIN(n.note) AS note_min,
    MAX(n.note) AS note_max,
    (SUM(CASE WHEN n.note >= 10 THEN 1 ELSE 0 END) * 100.0 / COUNT(n.note)) AS taux_reussite
FROM modules m
JOIN notes n ON m.id = n.module_id
GROUP BY m.id, m.nom
ORDER BY taux_reussite ASC;


WITH MoyennesModules AS (
    SELECT 
        a.nom,
        m.nom AS module_nom,
        AVG(n.note) AS moyenne_module
    FROM apprenants a
    JOIN notes n ON a.id = n.apprenant_id
    JOIN modules m ON n.module_id = m.id
    GROUP BY a.id, a.nom, m.id, m.nom
)
SELECT 
    nom,
    GROUP_CONCAT(module_nom) AS modules_en_difficulte,
    COUNT(module_nom) AS nombre_modules_critiques
FROM MoyennesModules
WHERE moyenne_module < 10
GROUP BY nom
HAVING COUNT(module_nom) >= 2;


WITH MoyennesModules AS (
    SELECT 
        a.id AS apprenant_id,
        a.nom AS apprenant_nom,
        m.coefficient,
        AVG(n.note) AS moyenne_module
    FROM apprenants a
    JOIN notes n ON a.id = n.apprenant_id
    JOIN modules m ON n.module_id = m.id
    GROUP BY a.id, a.nom, m.id, m.coefficient
),
MoyennesFinales AS (
    SELECT 
        apprenant_nom AS nom,
        SUM(moyenne_module * coefficient) / SUM(coefficient) AS moyenne_finale
    FROM MoyennesModules
    GROUP BY apprenant_id, apprenant_nom
)
SELECT 
    nom,
    moyenne_finale,
    CASE
        WHEN moyenne_finale >= 16 THEN 'Tres Bien'
        WHEN moyenne_finale >= 14 THEN 'Bien'
        WHEN moyenne_finale >= 12 THEN 'Assez Bien'
        WHEN moyenne_finale >= 10 THEN 'Passable'
        ELSE 'Echec'
    END AS mention
FROM MoyennesFinales
ORDER BY moyenne_finale DESC;


SELECT 
    m.nom AS module,
    AVG(CASE WHEN n.type_evaluation = 'QCM' THEN n.note END) AS moyenne_qcm,
    AVG(CASE WHEN n.type_evaluation = 'Projet' THEN n.note END) AS moyenne_projet,
    CASE
        WHEN AVG(CASE WHEN n.type_evaluation = 'QCM' THEN n.note END) > AVG(CASE WHEN n.type_evaluation = 'Projet' THEN n.note END) THEN 'QCM'
        WHEN AVG(CASE WHEN n.type_evaluation = 'QCM' THEN n.note END) < AVG(CASE WHEN n.type_evaluation = 'Projet' THEN n.note END) THEN 'Projet'
        ELSE 'Egalite'
    END AS meilleure_performance
FROM modules m
JOIN notes n ON m.id = n.module_id
GROUP BY m.id, m.nom;