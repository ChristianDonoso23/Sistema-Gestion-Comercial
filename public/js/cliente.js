const createClientsPanel = () => {
    Ext.define("App.model.Cliente", {
        extend: "Ext.data.Model",
        fields: [
            { name: "id", type: "int" },
            { name: "tipo", type: "string" },
            { name: "email", type: "string" },
            { name: "telefono", type: "string" },
            { name: "direccion", type: "string" },
            { name: "nombre", type: "string" },
            { name: "apellido", type: "string" },
            { name: "cedula", type: "string" },
            { name: "razonSocial", type: "string" },
            { name: "ruc", type: "string" },
            { name: "representanteLegal", type: "string" },
            {
                name: "nombreCompleto",
                convert: (v, rec) => {
                    if (rec.get("tipo") === "PersonaNatural") {
                        return `${rec.get("nombre")} ${rec.get("apellido")}`;
                    }
                    return rec.get("razonSocial") || "";
                }
            },
            {
                name: "identificacion",
                convert: (v, rec) => {
                    if (rec.get("tipo") === "PersonaNatural") {
                        return rec.get("cedula");
                    }
                    return rec.get("ruc") || "";
                }
            },
            {
                name: "representante",
                convert: (v, rec) => {
                    if (rec.get("tipo") === "PersonaJuridica") {
                        return rec.get("representanteLegal");
                    }
                    return "";
                }
            }
        ]
    });

    const clientStore = Ext.create("Ext.data.Store", {
        storeId: "clientStore",
        model: "App.model.Cliente",
        autoLoad: true,
        proxy: {
            type: "ajax",
            url: "Api/cliente.php",
            reader: {
                type: "json",
                rootProperty: "data"
            }
        }
    });

    const grid = Ext.create("Ext.grid.Panel", {
        title: "Listado de Clientes",
        store: clientStore,
        itemId: "clientPanel",
        layout: "fit",
        tbar: [
            {
                text: "General",
                handler: () => {
                    clientStore.clearFilter();
                }
            },
            {
                text: "Personas Naturales",
                handler: () => {
                    clientStore.clearFilter();
                    clientStore.filterBy(rec => rec.get("tipo") === "PersonaNatural");
                }
            },
            {
                text: "Personas Jurídicas",
                handler: () => {
                    clientStore.clearFilter();
                    clientStore.filterBy(rec => rec.get("tipo") === "PersonaJuridica");
                }
            }
        ],
        columns: [
            { 
                text: "ID", 
                width: 40, 
                sortable: false, 
                hideable: false, 
                dataIndex: "id" 
            },
            { 
                text: "Tipo", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "tipo" 
            },
            { 
                text: "Email", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "email" 
            },
            { 
                text: "Teléfono", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "telefono" 
            },
            { 
                text: "Dirección", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "direccion" 
            },
            { 
                text: "Nombre/Empresa", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "nombreCompleto" 
            },
            { 
                text: "Identificación", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "identificacion" 
            },
            { 
                text: "Representante Legal", 
                flex: 1, 
                sortable: false, 
                hideable: false, 
                dataIndex: "representanteLegal" 
            }
        ]
    });
    
    return grid;
};

window.createClientsPanel = createClientsPanel;
