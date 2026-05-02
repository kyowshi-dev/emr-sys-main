public function run(): void
{
    $zones = [];
    for ($i = 1; $i <= 8; $i++) {
        $zones[] = [
            'id' => $i,
            'zone_number' => $i,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    DB::table('zones')->insert($zones);
}