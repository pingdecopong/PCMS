DROP TABLE IF EXISTS VProjectUser;
DROP VIEW IF EXISTS VProjectUser;
CREATE VIEW VProjectUser AS SELECT
	SystemUserId AS SystemUserId,
	ProjectMasterId AS ProjectMasterId,
	RoleNo AS RoleNo
FROM
	TBProjectUser AS pu;

DROP TABLE IF EXISTS VProjectView;
DROP VIEW IF EXISTS VProjectView;
CREATE VIEW VProjectView AS SELECT
	tp.id AS id,
	tp.Name AS Name,
	tp.Status AS Status,
	tp.CustomerId AS CustomerId,
	tp.PeriodStart AS PeriodStart,
	tp.PeriodEnd AS PeriodEnd,
	tp.ManagerId AS ManagerId,
	(
		SELECT
			ifnull(sum(tpcm.Cost), 0) AS cost
		FROM
			TBProjectCostMaster tpcm
		WHERE
			tp.id = tpcm.ProjectMasterId AND tpcm.DeleteFlag = FALSE
		GROUP BY
			tpcm.ProjectMasterId
	) AS ProjectTotalCost,
	(
		SELECT
			ifnull(sum(tpc.Cost), 0) AS cost
		FROM
			TBProjectCostMaster tpcm
		LEFT JOIN TBProductionCost tpc ON tpcm.id = tpc.ProjectCostMasterId AND tpc.DeleteFlag = FALSE
		WHERE
			tp.id = tpcm.ProjectMasterId AND tpcm.DeleteFlag = FALSE
		GROUP BY
			tpcm.ProjectMasterId
	) AS ProductionTotalCost
FROM
	TBProjectMaster tp
WHERE
	tp.DeleteFlag = FALSE;
