export interface Endpoint {
    id: number;
    name: string;
    url: string;
    method: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH' | string;
    headers: any;
    body: any;
    check_interval: number;
    user_id: number;
}
