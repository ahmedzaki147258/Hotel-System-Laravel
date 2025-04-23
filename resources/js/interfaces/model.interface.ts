export interface Country {
    id: number;
    name: string;
    iso_alpha_2: string;
}
export interface Client {
    id: number;
    name: string;
    email: string;
    avatar_image: string;
    country_id: number;
    country: string;
    gender: string;
    mobile: string;
}
export interface Floor {
    id: number;
    name: string;
    number: string;
}
export interface Room {
    id: number;
    number: string;
    capacity: number;
    status: string;
    price: number;
    floor: Floor;
}
export interface Reservation {
    id: number;
    client_id: number;
    accompany_number: number;
    paid_price_in_cents: number;
    paid_price_in_dollars: string;
    payment_id: string;
    check_in_at: string;
    check_out_at: string;
    room_id: number;
    room: Room;
}

export interface Receptionist{
    id: number;
      name: string;
        email: string;
        password: string;
        national_id: string;
        avatar_image: string;
        created_at: string;
}

interface ReceptionistResponse {
  data: Receptionist[];
  current_page: number;
  last_page: number;
  prev_page_url: string | null;
  next_page_url: string | null;
}