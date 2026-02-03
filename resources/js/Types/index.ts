export interface User {
    useid: number;
    username: string;
    fullname: string;
    role: 'ADMIN' | 'STAFF' | 'USER';
    department?: string;
    designation?: string;
    contact?: string;
    emailadd?: string;
}

export interface Item {
    itemcode: number;
    itemname: string;
    description?: string;
    brand?: string;
    dosage_form?: string;
    strength?: string;
    default_uom?: string;
    deleted_at?: string;
    created_at?: string;
    updated_at?: string;
}

export interface Receiving {
    recid: number;
    itemcode: number;
    datereceived: string;
    supplier?: string;
    referenceno?: string;
    qty: number;
    uom: string;
    unitprice: number;
    totalamount: number;
    batchno: string;
    expirydate: string;
    department?: string;
    created_at?: string;
    updated_at?: string;
}

export interface RequestRis {
    req_ris: number;
    ris_no: string;
    division?: string;
    department?: string;
    itemcode: number;
    req_qty: number;
    isavailable: boolean;
    remarks?: string;
    requestedby?: number;
    approvedby?: number;
    receivedby?: number;
    requestedat?: string;
    approvedat?: string;
    receivedat?: string;
    created_at?: string;
    updated_at?: string;
}

export interface RequestPtr {
    req_ptr: number;
    ptr_no: string;
    division?: string;
    target?: string;
    trans_type: 'Donation' | 'Reassignment' | 'Relocate' | 'Others';
    trans_type_other?: string;
    itemcode: number;
    req_qty: number;
    remarks?: string;
    purpose?: string;
    approvedby?: number;
    receivedby?: number;
    requestedat?: string;
    approvedat?: string;
    receivedat?: string;
    created_at?: string;
    updated_at?: string;
}

