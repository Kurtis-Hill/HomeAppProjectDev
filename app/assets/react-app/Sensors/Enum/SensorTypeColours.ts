/**
 * Centralised colour maps for sensor and reading types.
 * Import these wherever you need to tint a type label/badge — do NOT define
 * local copies in individual components.
 */

export const SENSOR_TYPE_COLOURS: Record<string, string> = {
    Dht:          '#e74a3b',
    Dallas:       '#f6c23e',
    Bmp:          '#1cc88a',
    Soil:         '#36b9cc',
    GenericRelay: '#858796',
    GenericMotion:'#fd7e14',
    Ldr:          '#6f42c1',
    Sht:          '#20c9a6',
};

export const READING_TYPE_COLOURS: Record<string, string> = {
    temperature: '#e74c3c',
    humidity:    '#3498db',
    analog:      '#9b59b6',
    latitude:    '#27ae60',
    pressure:    '#f39c12',
};

/** Returns the colour for a sensor type, falling back to a neutral grey. */
export function getSensorTypeColour(sensorType: string): string {
    return SENSOR_TYPE_COLOURS[sensorType] ?? '#858796';
}

/** Returns the colour for a reading type, falling back to a neutral grey. */
export function getReadingTypeColour(readingType: string): string {
    return READING_TYPE_COLOURS[readingType] ?? '#858796';
}
