import type { Endpoint } from '@/types/app/endpoint';

export interface LogRecord {
    id: number;
    endpoint: Endpoint;
    status_code: '200' | '201' | '204' | '400' | '401' | '403' | '500' | '503' | string;
    response_time: number;
    checked_at: Date;
}
