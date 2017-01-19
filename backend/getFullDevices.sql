SELECT
  h.device_id,
  hp.name,
  ha.value,
  ht.type
FROM
  devices d,
  hardware h,
  hardware_attributes ha,
  hardware_property hp,
  hardware_types ht
WHERE
  d.id = h.device_id
  AND ha.hardware_id = h.id
  AND h.type = ht.id
  AND ha.hardware_id = h.id
  AND hp.id = ha.hardware_property_id;
