var __defProp = Object.defineProperty;

var __defProps = Object.defineProperties;

var __getOwnPropDescs = Object.getOwnPropertyDescriptors;

var __getOwnPropSymbols = Object.getOwnPropertySymbols;

var __hasOwnProp = Object.prototype.hasOwnProperty;

var __propIsEnum = Object.prototype.propertyIsEnumerable;

var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;

var __spreadValues = (a, b) => {

  for (var prop in b || (b = {}))

    if (__hasOwnProp.call(b, prop))

      __defNormalProp(a, prop, b[prop]);

  if (__getOwnPropSymbols)

    for (var prop of __getOwnPropSymbols(b)) {

      if (__propIsEnum.call(b, prop))

        __defNormalProp(a, prop, b[prop]);

    }

  return a;

};

var __spreadProps = (a, b) => __defProps(a, __getOwnPropDescs(b));

var __publicField = (obj, key, value) => {

  __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);

  return value;

};

function noop() {

}

const identity = (x) => x;

function assign(tar, src) {

  for (const k in src)

    tar[k] = src[k];

  return tar;

}

function run(fn) {

  return fn();

}

function blank_object() {

  return /* @__PURE__ */ Object.create(null);

}

function run_all(fns) {

  fns.forEach(run);

}

function is_function(thing) {

  return typeof thing === "function";

}

function safe_not_equal(a, b) {

  return a != a ? b == b : a !== b || (a && typeof a === "object" || typeof a === "function");

}

let src_url_equal_anchor;

function src_url_equal(element_src, url) {

  if (!src_url_equal_anchor) {

    src_url_equal_anchor = document.createElement("a");

  }

  src_url_equal_anchor.href = url;

  return element_src === src_url_equal_anchor.href;

}

function is_empty(obj) {

  return Object.keys(obj).length === 0;

}

function subscribe(store, ...callbacks) {

  if (store == null) {

    return noop;

  }

  const unsub = store.subscribe(...callbacks);

  return unsub.unsubscribe ? () => unsub.unsubscribe() : unsub;

}

function get_store_value(store) {

  let value;

  subscribe(store, (_) => value = _)();

  return value;

}

function component_subscribe(component, store, callback) {

  component.$$.on_destroy.push(subscribe(store, callback));

}

function create_slot(definition, ctx, $$scope, fn) {

  if (definition) {

    const slot_ctx = get_slot_context(definition, ctx, $$scope, fn);

    return definition[0](slot_ctx);

  }

}

function get_slot_context(definition, ctx, $$scope, fn) {

  return definition[1] && fn ? assign($$scope.ctx.slice(), definition[1](fn(ctx))) : $$scope.ctx;

}

function get_slot_changes(definition, $$scope, dirty, fn) {

  if (definition[2] && fn) {

    const lets = definition[2](fn(dirty));

    if ($$scope.dirty === void 0) {

      return lets;

    }

    if (typeof lets === "object") {

      const merged = [];

      const len = Math.max($$scope.dirty.length, lets.length);

      for (let i = 0; i < len; i += 1) {

        merged[i] = $$scope.dirty[i] | lets[i];

      }

      return merged;

    }

    return $$scope.dirty | lets;

  }

  return $$scope.dirty;

}

function update_slot_base(slot, slot_definition, ctx, $$scope, slot_changes, get_slot_context_fn) {

  if (slot_changes) {

    const slot_context = get_slot_context(slot_definition, ctx, $$scope, get_slot_context_fn);

    slot.p(slot_context, slot_changes);

  }

}

function get_all_dirty_from_scope($$scope) {

  if ($$scope.ctx.length > 32) {

    const dirty = [];

    const length = $$scope.ctx.length / 32;

    for (let i = 0; i < length; i++) {

      dirty[i] = -1;

    }

    return dirty;

  }

  return -1;

}

function exclude_internal_props(props) {

  const result = {};

  for (const k in props)

    if (k[0] !== "$")

      result[k] = props[k];

  return result;

}

function null_to_empty(value) {

  return value == null ? "" : value;

}

function set_store_value(store, ret, value) {

  store.set(value);

  return ret;

}

function action_destroyer(action_result) {

  return action_result && is_function(action_result.destroy) ? action_result.destroy : noop;

}

const is_client = typeof window !== "undefined";

let now = is_client ? () => window.performance.now() : () => Date.now();

let raf = is_client ? (cb) => requestAnimationFrame(cb) : noop;

const tasks = /* @__PURE__ */ new Set();

function run_tasks(now2) {

  tasks.forEach((task) => {

    if (!task.c(now2)) {

      tasks.delete(task);

      task.f();

    }

  });

  if (tasks.size !== 0)

    raf(run_tasks);

}

function loop(callback) {

  let task;

  if (tasks.size === 0)

    raf(run_tasks);

  return {

    promise: new Promise((fulfill) => {

      tasks.add(task = { c: callback, f: fulfill });

    }),

    abort() {

      tasks.delete(task);

    }

  };

}

function append(target, node) {

  target.appendChild(node);

}

function get_root_for_style(node) {

  if (!node)

    return document;

  const root = node.getRootNode ? node.getRootNode() : node.ownerDocument;

  if (root && root.host) {

    return root;

  }

  return node.ownerDocument;

}

function append_empty_stylesheet(node) {

  const style_element = element("style");

  append_stylesheet(get_root_for_style(node), style_element);

  return style_element.sheet;

}

function append_stylesheet(node, style) {

  append(node.head || node, style);

}

function insert(target, node, anchor) {

  target.insertBefore(node, anchor || null);

}

function detach(node) {

  node.parentNode.removeChild(node);

}

function destroy_each(iterations, detaching) {

  for (let i = 0; i < iterations.length; i += 1) {

    if (iterations[i])

      iterations[i].d(detaching);

  }

}

function element(name) {

  return document.createElement(name);

}

function svg_element(name) {

  return document.createElementNS("http://www.w3.org/2000/svg", name);

}

function text(data) {

  return document.createTextNode(data);

}

function space() {

  return text(" ");

}

function empty() {

  return text("");

}

function listen(node, event, handler, options) {

  node.addEventListener(event, handler, options);

  return () => node.removeEventListener(event, handler, options);

}

function prevent_default(fn) {

  return function(event) {

    event.preventDefault();

    return fn.call(this, event);

  };

}

function stop_propagation(fn) {

  return function(event) {

    event.stopPropagation();

    return fn.call(this, event);

  };

}

function attr(node, attribute, value) {

  if (value == null)

    node.removeAttribute(attribute);

  else if (node.getAttribute(attribute) !== value)

    node.setAttribute(attribute, value);

}

function set_attributes(node, attributes) {

  const descriptors = Object.getOwnPropertyDescriptors(node.__proto__);

  for (const key in attributes) {

    if (attributes[key] == null) {

      node.removeAttribute(key);

    } else if (key === "style") {

      node.style.cssText = attributes[key];

    } else if (key === "__value") {

      node.value = node[key] = attributes[key];

    } else if (descriptors[key] && descriptors[key].set) {

      node[key] = attributes[key];

    } else {

      attr(node, key, attributes[key]);

    }

  }

}

function set_svg_attributes(node, attributes) {

  for (const key in attributes) {

    attr(node, key, attributes[key]);

  }

}

function children(element2) {

  return Array.from(element2.childNodes);

}

function set_data(text2, data) {

  data = "" + data;

  if (text2.wholeText !== data)

    text2.data = data;

}

function set_input_value(input, value) {

  input.value = value == null ? "" : value;

}

function toggle_class(element2, name, toggle) {

  element2.classList[toggle ? "add" : "remove"](name);

}

function custom_event(type, detail, bubbles = false) {

  const e = document.createEvent("CustomEvent");

  e.initCustomEvent(type, bubbles, false, detail);

  return e;

}

const managed_styles = /* @__PURE__ */ new Map();

let active = 0;

function hash(str) {

  let hash2 = 5381;

  let i = str.length;

  while (i--)

    hash2 = (hash2 << 5) - hash2 ^ str.charCodeAt(i);

  return hash2 >>> 0;

}

function create_style_information(doc, node) {

  const info = { stylesheet: append_empty_stylesheet(node), rules: {} };

  managed_styles.set(doc, info);

  return info;

}

function create_rule(node, a, b, duration, delay2, ease, fn, uid = 0) {

  const step = 16.666 / duration;

  let keyframes = "{\n";

  for (let p = 0; p <= 1; p += step) {

    const t2 = a + (b - a) * ease(p);

    keyframes += p * 100 + `%{${fn(t2, 1 - t2)}}

`;

  }

  const rule = keyframes + `100% {${fn(b, 1 - b)}}

}`;

  const name = `__svelte_${hash(rule)}_${uid}`;

  const doc = get_root_for_style(node);

  const { stylesheet, rules } = managed_styles.get(doc) || create_style_information(doc, node);

  if (!rules[name]) {

    rules[name] = true;

    stylesheet.insertRule(`@keyframes ${name} ${rule}`, stylesheet.cssRules.length);

  }

  const animation = node.style.animation || "";

  node.style.animation = `${animation ? `${animation}, ` : ""}${name} ${duration}ms linear ${delay2}ms 1 both`;

  active += 1;

  return name;

}

function delete_rule(node, name) {

  const previous = (node.style.animation || "").split(", ");

  const next = previous.filter(name ? (anim) => anim.indexOf(name) < 0 : (anim) => anim.indexOf("__svelte") === -1);

  const deleted = previous.length - next.length;

  if (deleted) {

    node.style.animation = next.join(", ");

    active -= deleted;

    if (!active)

      clear_rules();

  }

}

function clear_rules() {

  raf(() => {

    if (active)

      return;

    managed_styles.forEach((info) => {

      const { stylesheet } = info;

      let i = stylesheet.cssRules.length;

      while (i--)

        stylesheet.deleteRule(i);

      info.rules = {};

    });

    managed_styles.clear();

  });

}

let current_component;

function set_current_component(component) {

  current_component = component;

}

function get_current_component() {

  if (!current_component)

    throw new Error("Function called outside component initialization");

  return current_component;

}

function createEventDispatcher() {

  const component = get_current_component();

  return (type, detail) => {

    const callbacks = component.$$.callbacks[type];

    if (callbacks) {

      const event = custom_event(type, detail);

      callbacks.slice().forEach((fn) => {

        fn.call(component, event);

      });

    }

  };

}

function setContext(key, context) {

  get_current_component().$$.context.set(key, context);

}

function getContext(key) {

  return get_current_component().$$.context.get(key);

}

const dirty_components = [];

const binding_callbacks = [];

const render_callbacks = [];

const flush_callbacks = [];

const resolved_promise = Promise.resolve();

let update_scheduled = false;

function schedule_update() {

  if (!update_scheduled) {

    update_scheduled = true;

    resolved_promise.then(flush);

  }

}

function add_render_callback(fn) {

  render_callbacks.push(fn);

}

const seen_callbacks = /* @__PURE__ */ new Set();

let flushidx = 0;

function flush() {

  const saved_component = current_component;

  do {

    while (flushidx < dirty_components.length) {

      const component = dirty_components[flushidx];

      flushidx++;

      set_current_component(component);

      update(component.$$);

    }

    set_current_component(null);

    dirty_components.length = 0;

    flushidx = 0;

    while (binding_callbacks.length)

      binding_callbacks.pop()();

    for (let i = 0; i < render_callbacks.length; i += 1) {

      const callback = render_callbacks[i];

      if (!seen_callbacks.has(callback)) {

        seen_callbacks.add(callback);

        callback();

      }

    }

    render_callbacks.length = 0;

  } while (dirty_components.length);

  while (flush_callbacks.length) {

    flush_callbacks.pop()();

  }

  update_scheduled = false;

  seen_callbacks.clear();

  set_current_component(saved_component);

}

function update($$) {

  if ($$.fragment !== null) {

    $$.update();

    run_all($$.before_update);

    const dirty = $$.dirty;

    $$.dirty = [-1];

    $$.fragment && $$.fragment.p($$.ctx, dirty);

    $$.after_update.forEach(add_render_callback);

  }

}

let promise;

function wait() {

  if (!promise) {

    promise = Promise.resolve();

    promise.then(() => {

      promise = null;

    });

  }

  return promise;

}

function dispatch(node, direction, kind) {

  node.dispatchEvent(custom_event(`${direction ? "intro" : "outro"}${kind}`));

}

const outroing = /* @__PURE__ */ new Set();

let outros;

function group_outros() {

  outros = {

    r: 0,

    c: [],

    p: outros

  };

}

function check_outros() {

  if (!outros.r) {

    run_all(outros.c);

  }

  outros = outros.p;

}

function transition_in(block, local) {

  if (block && block.i) {

    outroing.delete(block);

    block.i(local);

  }

}

function transition_out(block, local, detach2, callback) {

  if (block && block.o) {

    if (outroing.has(block))

      return;

    outroing.add(block);

    outros.c.push(() => {

      outroing.delete(block);

      if (callback) {

        if (detach2)

          block.d(1);

        callback();

      }

    });

    block.o(local);

  }

}

const null_transition = { duration: 0 };

function create_bidirectional_transition(node, fn, params, intro) {

  let config2 = fn(node, params);

  let t2 = intro ? 0 : 1;

  let running_program = null;

  let pending_program = null;

  let animation_name = null;

  function clear_animation() {

    if (animation_name)

      delete_rule(node, animation_name);

  }

  function init2(program, duration) {

    const d = program.b - t2;

    duration *= Math.abs(d);

    return {

      a: t2,

      b: program.b,

      d,

      duration,

      start: program.start,

      end: program.start + duration,

      group: program.group

    };

  }

  function go(b) {

    const { delay: delay2 = 0, duration = 300, easing = identity, tick = noop, css } = config2 || null_transition;

    const program = {

      start: now() + delay2,

      b

    };

    if (!b) {

      program.group = outros;

      outros.r += 1;

    }

    if (running_program || pending_program) {

      pending_program = program;

    } else {

      if (css) {

        clear_animation();

        animation_name = create_rule(node, t2, b, duration, delay2, easing, css);

      }

      if (b)

        tick(0, 1);

      running_program = init2(program, duration);

      add_render_callback(() => dispatch(node, b, "start"));

      loop((now2) => {

        if (pending_program && now2 > pending_program.start) {

          running_program = init2(pending_program, duration);

          pending_program = null;

          dispatch(node, running_program.b, "start");

          if (css) {

            clear_animation();

            animation_name = create_rule(node, t2, running_program.b, running_program.duration, 0, easing, config2.css);

          }

        }

        if (running_program) {

          if (now2 >= running_program.end) {

            tick(t2 = running_program.b, 1 - t2);

            dispatch(node, running_program.b, "end");

            if (!pending_program) {

              if (running_program.b) {

                clear_animation();

              } else {

                if (!--running_program.group.r)

                  run_all(running_program.group.c);

              }

            }

            running_program = null;

          } else if (now2 >= running_program.start) {

            const p = now2 - running_program.start;

            t2 = running_program.a + running_program.d * easing(p / running_program.duration);

            tick(t2, 1 - t2);

          }

        }

        return !!(running_program || pending_program);

      });

    }

  }

  return {

    run(b) {

      if (is_function(config2)) {

        wait().then(() => {

          config2 = config2();

          go(b);

        });

      } else {

        go(b);

      }

    },

    end() {

      clear_animation();

      running_program = pending_program = null;

    }

  };

}

function get_spread_update(levels, updates) {

  const update2 = {};

  const to_null_out = {};

  const accounted_for = { $$scope: 1 };

  let i = levels.length;

  while (i--) {

    const o = levels[i];

    const n = updates[i];

    if (n) {

      for (const key in o) {

        if (!(key in n))

          to_null_out[key] = 1;

      }

      for (const key in n) {

        if (!accounted_for[key]) {

          update2[key] = n[key];

          accounted_for[key] = 1;

        }

      }

      levels[i] = n;

    } else {

      for (const key in o) {

        accounted_for[key] = 1;

      }

    }

  }

  for (const key in to_null_out) {

    if (!(key in update2))

      update2[key] = void 0;

  }

  return update2;

}

function create_component(block) {

  block && block.c();

}

function mount_component(component, target, anchor, customElement) {

  const { fragment, on_mount, on_destroy, after_update } = component.$$;

  fragment && fragment.m(target, anchor);

  if (!customElement) {

    add_render_callback(() => {

      const new_on_destroy = on_mount.map(run).filter(is_function);

      if (on_destroy) {

        on_destroy.push(...new_on_destroy);

      } else {

        run_all(new_on_destroy);

      }

      component.$$.on_mount = [];

    });

  }

  after_update.forEach(add_render_callback);

}

function destroy_component(component, detaching) {

  const $$ = component.$$;

  if ($$.fragment !== null) {

    run_all($$.on_destroy);

    $$.fragment && $$.fragment.d(detaching);

    $$.on_destroy = $$.fragment = null;

    $$.ctx = [];

  }

}

function make_dirty(component, i) {

  if (component.$$.dirty[0] === -1) {

    dirty_components.push(component);

    schedule_update();

    component.$$.dirty.fill(0);

  }

  component.$$.dirty[i / 31 | 0] |= 1 << i % 31;

}

function init(component, options, instance2, create_fragment2, not_equal, props, append_styles, dirty = [-1]) {

  const parent_component = current_component;

  set_current_component(component);

  const $$ = component.$$ = {

    fragment: null,

    ctx: null,

    props,

    update: noop,

    not_equal,

    bound: blank_object(),

    on_mount: [],

    on_destroy: [],

    on_disconnect: [],

    before_update: [],

    after_update: [],

    context: new Map(options.context || (parent_component ? parent_component.$$.context : [])),

    callbacks: blank_object(),

    dirty,

    skip_bound: false,

    root: options.target || parent_component.$$.root

  };

  append_styles && append_styles($$.root);

  let ready = false;

  $$.ctx = instance2 ? instance2(component, options.props || {}, (i, ret, ...rest) => {

    const value = rest.length ? rest[0] : ret;

    if ($$.ctx && not_equal($$.ctx[i], $$.ctx[i] = value)) {

      if (!$$.skip_bound && $$.bound[i])

        $$.bound[i](value);

      if (ready)

        make_dirty(component, i);

    }

    return ret;

  }) : [];

  $$.update();

  ready = true;

  run_all($$.before_update);

  $$.fragment = create_fragment2 ? create_fragment2($$.ctx) : false;

  if (options.target) {

    if (options.hydrate) {

      const nodes = children(options.target);

      $$.fragment && $$.fragment.l(nodes);

      nodes.forEach(detach);

    } else {

      $$.fragment && $$.fragment.c();

    }

    if (options.intro)

      transition_in(component.$$.fragment);

    mount_component(component, options.target, options.anchor, options.customElement);

    flush();

  }

  set_current_component(parent_component);

}

class SvelteComponent {

  $destroy() {

    destroy_component(this, 1);

    this.$destroy = noop;

  }

  $on(type, callback) {

    const callbacks = this.$$.callbacks[type] || (this.$$.callbacks[type] = []);

    callbacks.push(callback);

    return () => {

      const index = callbacks.indexOf(callback);

      if (index !== -1)

        callbacks.splice(index, 1);

    };

  }

  $set($$props) {

    if (this.$$set && !is_empty($$props)) {

      this.$$.skip_bound = true;

      this.$$set($$props);

      this.$$.skip_bound = false;

    }

  }

}

function clickOutside(node, eventName = "outclick") {

  const handleClick = (event) => {

    if (!node.contains(event.target)) {

      node.dispatchEvent(new CustomEvent(eventName, { bubbles: eventName !== "outclick" }));

    }

  };

  document.addEventListener("click", handleClick, true);

  return {

    destroy() {

      document.removeEventListener("click", handleClick, true);

    }

  };

}

function cubicOut(t2) {

  const f = t2 - 1;

  return f * f * f + 1;

}

function fly(node, { delay: delay2 = 0, duration = 400, easing = cubicOut, x = 0, y = 0, opacity = 0 } = {}) {

  const style = getComputedStyle(node);

  const target_opacity = +style.opacity;

  const transform = style.transform === "none" ? "" : style.transform;

  const od = target_opacity * (1 - opacity);

  return {

    delay: delay2,

    duration,

    easing,

    css: (t2, u) => `

			transform: ${transform} translate(${(1 - t2) * x}px, ${(1 - t2) * y}px);

			opacity: ${target_opacity - od * u}`

  };

}

const subscriber_queue = [];

function writable(value, start = noop) {

  let stop;

  const subscribers = /* @__PURE__ */ new Set();

  function set(new_value) {

    if (safe_not_equal(value, new_value)) {

      value = new_value;

      if (stop) {

        const run_queue = !subscriber_queue.length;

        for (const subscriber of subscribers) {

          subscriber[1]();

          subscriber_queue.push(subscriber, value);

        }

        if (run_queue) {

          for (let i = 0; i < subscriber_queue.length; i += 2) {

            subscriber_queue[i][0](subscriber_queue[i + 1]);

          }

          subscriber_queue.length = 0;

        }

      }

    }

  }

  function update2(fn) {

    set(fn(value));

  }

  function subscribe2(run2, invalidate = noop) {

    const subscriber = [run2, invalidate];

    subscribers.add(subscriber);

    if (subscribers.size === 1) {

      stop = start(set) || noop;

    }

    run2(value);

    return () => {

      subscribers.delete(subscriber);

      if (subscribers.size === 0) {

        stop();

        stop = null;

      }

    };

  }

  return { set, update: update2, subscribe: subscribe2 };

}

function isPromise(target) {

  if (typeof target === "object" && typeof target["then"] === "function") {

    return true;

  }

  return false;

}

class Query {

  constructor(cb, options) {

    __publicField(this, "store");

    __publicField(this, "getState", () => {

      return get_store_value(this.store);

    });

    __publicField(this, "setData", (newData) => {

      this.store.update((v) => __spreadProps(__spreadValues({}, v), {

        isLoading: false,

        isSuccess: true,

        data: typeof newData === "function" ? newData(v.data) : newData

      }));

    });

    __publicField(this, "getData", () => {

      return this.getState().data;

    });

    const fetchData = () => {

      if (this.getData()) {

        return;

      }

      const response = cb();

      if (isPromise(response)) {

        response.then(this.setData).catch((e) => {

          var _a;

          (_a = options.onError) == null ? void 0 : _a.call(options);

          this.store.update((v) => __spreadProps(__spreadValues({}, v), {

            isLoading: false,

            isSuccess: false

          }));

        });

      } else {

        this.setData(response);

      }

    };

    this.store = writable({

      isSuccess: false,

      isLoading: false,

      data: void 0,

      refetch: fetchData

    });

    if (options.enabled !== false) {

      fetchData();

    }

  }

}

class QueryClient {

  constructor() {

    __publicField(this, "queries", /* @__PURE__ */ new Map());

  }

  getQuery(key, cb, options = {}) {

    if (!this.queries.has(key)) {

      this.queries.set(key, new Query(cb, options));

    }

    return this.queries.get(key);

  }

  setQueryData(key, updater) {

    const query = this.queries.get(key);

    if (query) {

      query.setData(updater);

    }

  }

  getQueryData(key) {

    var _a;

    return (_a = this.queries.get(key)) == null ? void 0 : _a.getData();

  }

  getQueryState(key) {

    const query = this.queries.get(key);

    if (query) {

      return query.getState();

    }

    return {

      data: void 0,

      isLoading: false,

      isSuccess: false,

      refetch: () => {

      }

    };

  }

}

function useQuery(key, cb, options = {}) {

  const query = useQueryClient().getQuery(key, cb, options);

  return {

    subscribe: query.store.subscribe

  };

}

function useMutation(cb, options = {}) {

  const mutate = (arg) => {

    store.update((v) => __spreadProps(__spreadValues({}, v), { isLoading: true }));

    return cb(arg).then((data) => {

      var _a;

      (_a = options.onSuccess) == null ? void 0 : _a.call(options, data);

      return data;

    }).catch((reason) => {

      var _a;

      (_a = options.onError) == null ? void 0 : _a.call(options, reason);

      throw reason;

    }).finally(() => {

      store.update((v) => __spreadProps(__spreadValues({}, v), { isLoading: false }));

    });

  };

  const store = writable({

    isLoading: false,

    mutate: (arg) => {

      mutate(arg).catch(() => null);

    },

    mutateAsync: mutate

  });

  return { subscribe: store.subscribe };

}

function create_fragment$q(ctx) {

  let current;

  const default_slot_template = ctx[2].default;

  const default_slot = create_slot(default_slot_template, ctx, ctx[1], null);

  return {

    c() {

      if (default_slot)

        default_slot.c();

    },

    m(target, anchor) {

      if (default_slot) {

        default_slot.m(target, anchor);

      }

      current = true;

    },

    p(ctx2, [dirty]) {

      if (default_slot) {

        if (default_slot.p && (!current || dirty & 2)) {

          update_slot_base(default_slot, default_slot_template, ctx2, ctx2[1], !current ? get_all_dirty_from_scope(ctx2[1]) : get_slot_changes(default_slot_template, ctx2[1], dirty, null), null);

        }

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(default_slot, local);

      current = true;

    },

    o(local) {

      transition_out(default_slot, local);

      current = false;

    },

    d(detaching) {

      if (default_slot)

        default_slot.d(detaching);

    }

  };

}

function instance$o($$self, $$props, $$invalidate) {

  let { $$slots: slots = {}, $$scope } = $$props;

  let { client } = $$props;

  setContext(contextKey, client);

  $$self.$$set = ($$props2) => {

    if ("client" in $$props2)

      $$invalidate(0, client = $$props2.client);

    if ("$$scope" in $$props2)

      $$invalidate(1, $$scope = $$props2.$$scope);

  };

  return [client, $$scope, slots];

}

class QueryClientProvider extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$o, create_fragment$q, safe_not_equal, { client: 0 });

  }

}

const contextKey = Symbol("queryClient");

function useQueryClient() {

  return getContext(contextKey);

}

var IconLoader_svelte_svelte_type_style_lang = "";

function create_fragment$p(ctx) {

  let div;

  let div_class_value;

  let div_style_value;

  return {

    c() {

      div = element("div");

      attr(div, "class", div_class_value = null_to_empty(`loader ${ctx[1].class}`) + " svelte-1cyki07");

      attr(div, "style", div_style_value = `--size:${ctx[0]}px`);

    },

    m(target, anchor) {

      insert(target, div, anchor);

    },

    p(ctx2, [dirty]) {

      if (dirty & 2 && div_class_value !== (div_class_value = null_to_empty(`loader ${ctx2[1].class}`) + " svelte-1cyki07")) {

        attr(div, "class", div_class_value);

      }

      if (dirty & 1 && div_style_value !== (div_style_value = `--size:${ctx2[0]}px`)) {

        attr(div, "style", div_style_value);

      }

    },

    i: noop,

    o: noop,

    d(detaching) {

      if (detaching)

        detach(div);

    }

  };

}

function instance$n($$self, $$props, $$invalidate) {

  let { size = 20 } = $$props;

  $$self.$$set = ($$new_props) => {

    $$invalidate(1, $$props = assign(assign({}, $$props), exclude_internal_props($$new_props)));

    if ("size" in $$new_props)

      $$invalidate(0, size = $$new_props.size);

  };

  $$props = exclude_internal_props($$props);

  return [size, $$props];

}

class IconLoader extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$n, create_fragment$p, safe_not_equal, { size: 0 });

  }

}

function create_fragment$o(ctx) {

  let svg;

  let path0;

  let path1;

  let svg_levels = [

    { width: "23" },

    { height: "23" },

    { fill: "none" },

    { xmlns: "http://www.w3.org/2000/svg" },

    ctx[0]

  ];

  let svg_data = {};

  for (let i = 0; i < svg_levels.length; i += 1) {

    svg_data = assign(svg_data, svg_levels[i]);

  }

  return {

    c() {

      svg = svg_element("svg");

      path0 = svg_element("path");

      path1 = svg_element("path");

      attr(path0, "d", "M22.7 10.578c-.289-.29-.72-.578-1.152-.578H5.715c-.575 0-1.151.289-1.295.867L.102 20.977C-.186 21.845.102 23 1.397 23H17.23c.575 0 1.151-.289 1.295-.867l4.318-10.11c.288-.434.144-1.012-.144-1.445z");

      attr(path0, "fill", "currentColor");

      attr(path1, "d", "M1.754 9.814c.73-1.587 2.338-2.598 4.092-2.598H19V4.33c0-.866-.585-1.443-1.462-1.443H9.354L6.869.433C6.577.144 6.285 0 5.846 0H1.462C.585 0 0 .577 0 1.443V14l1.754-4.186z");

      attr(path1, "fill", "currentColor");

      set_svg_attributes(svg, svg_data);

    },

    m(target, anchor) {

      insert(target, svg, anchor);

      append(svg, path0);

      append(svg, path1);

    },

    p(ctx2, [dirty]) {

      set_svg_attributes(svg, svg_data = get_spread_update(svg_levels, [

        { width: "23" },

        { height: "23" },

        { fill: "none" },

        { xmlns: "http://www.w3.org/2000/svg" },

        dirty & 1 && ctx2[0]

      ]));

    },

    i: noop,

    o: noop,

    d(detaching) {

      if (detaching)

        detach(svg);

    }

  };

}

function instance$m($$self, $$props, $$invalidate) {

  $$self.$$set = ($$new_props) => {

    $$invalidate(0, $$props = assign(assign({}, $$props), exclude_internal_props($$new_props)));

  };

  $$props = exclude_internal_props($$props);

  return [$$props];

}

class IconFolder extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$m, create_fragment$o, safe_not_equal, {});

  }

}

var HTTPStatus = /* @__PURE__ */ ((HTTPStatus2) => {

  HTTPStatus2[HTTPStatus2["OK"] = 200] = "OK";

  HTTPStatus2[HTTPStatus2["Created"] = 201] = "Created";

  HTTPStatus2[HTTPStatus2["MultipleChoices"] = 300] = "MultipleChoices";

  HTTPStatus2[HTTPStatus2["NoContent"] = 204] = "NoContent";

  HTTPStatus2[HTTPStatus2["UnprocessableEntity"] = 422] = "UnprocessableEntity";

  HTTPStatus2[HTTPStatus2["Forbidden"] = 403] = "Forbidden";

  HTTPStatus2[HTTPStatus2["NotFound"] = 404] = "NotFound";

  HTTPStatus2[HTTPStatus2["BadRequest"] = 400] = "BadRequest";

  return HTTPStatus2;

})(HTTPStatus || {});

var FR = {

  delete: "Supprimer",

  deleteConfirm: "Voulez vous vraiment supprimer ce fichier ?",

  newFolderPlaceholder: "Nom du dossier",

  emptyTitle: "Ce dossier est vide",

  deleteFolder: "Supprimer le dossier",

  emptyDescription: "D\xE9poser un fichier ici pour le t\xE9l\xE9verser",

  createFolder: "Cr\xE9er un dossier",

  copy: "Copier le lien",

  size: "Taille",

  filename: "Nom",

  serverError: "Action impossible suite a une erreur serveur"

};

const messages = {

  delete: "Delete",

  deleteConfirm: "Do you really want to delete this file ?",

  newFolderPlaceholder: "Folder name",

  deleteFolder: "Delete this folder",

  emptyTitle: "This folder is empty",

  emptyDescription: "Drop a file here to upload it",

  createFolder: "New folder",

  copy: "Copy link",

  size: "Size",

  filename: "Name",

  serverError: "Server error"

};

const langs = {

  fr: FR,

  en: messages

};

let langMessages = messages;

function t(key) {

  return langMessages[key];

}

function setLang(lang) {

  langMessages = lang in langs ? langs[lang] : messages;

}

const flashMessages = writable([]);

const flash = (message, type = "success") => {

  const id = Date.now();

  flashMessages.update((messages2) => [{ type, message, id }, ...messages2]);

  if (type === "success") {

    window.setTimeout(() => {

      flashMessages.update((messages2) => messages2.filter((message2) => message2.id !== id));

    }, 2e3);

  }

};

const deleteFlashMessage = (id) => {

  flashMessages.update((messages2) => messages2.filter((message) => message.id !== id));

};

const filesQueryKey = (folderId) => `files/${folderId != null ? folderId : ""}`;

const foldersQueryKey = (parentId) => `folders/${parentId != null ? parentId : ""}`;

const folderStore = writable(null);

const folder = folderStore;

const searchQuery = writable("");

const removeFile = async (options, queryClient, file) => {

  const queryKey = filesQueryKey(file.folder);

  const oldData = queryClient.getQueryData(queryKey);

  if (oldData) {

    queryClient.setQueryData(queryKey, (files) => files ? files.filter((f) => f.id !== file.id) : []);

  }

  try {

    await options.deleteFile(file);

  } catch (e) {

    if (!(e instanceof Response) || e.status !== HTTPStatus.UnprocessableEntity) {

      flash(t(`serverError`), "danger");

      console.error(e);

    }

    queryClient.setQueryData(queryKey, oldData);

  }

};

const uploadFile = async (options, queryClient, file, folder2) => {

  try {

    const newFile = await options.uploadFile(file, folder2);

    const queryKey = filesQueryKey(folder2 == null ? void 0 : folder2.id);

    const state = queryClient.getQueryState(queryKey);

    if (state == null ? void 0 : state.data) {

      queryClient.setQueryData(queryKey, (files) => files ? [newFile, ...files] : [newFile]);

    }

  } catch (e) {

    if (!(e instanceof Response) || e.status !== HTTPStatus.UnprocessableEntity) {

      flash(t(`serverError`), "danger");

      console.error(e);

    }

  }

};

const useCreateFolderMutation = () => {

  const queryClient = useQueryClient();

  const options = getOptions();

  return useMutation((params) => options.createFolder(params), {

    onSuccess(folder2) {

      const addToCache = (parent) => {

        const queryKey = foldersQueryKey(parent);

        const state = queryClient.getQueryState(queryKey);

        if (state == null ? void 0 : state.data) {

          queryClient.setQueryData(queryKey, (folders) => folders ? [folder2, ...folders] : [folder2]);

        }

      };

      addToCache(folder2.parent);

      if (folder2.parent) {

        addToCache(null);

      }

    }

  });

};

const useDeleteFolderMutation = () => {

  const queryClient = useQueryClient();

  const options = getOptions();

  return useMutation((folder2) => options.deleteFolder(folder2).then((r) => folder2), {

    onSuccess: (folder2) => {

      folderStore.update(() => null);

      const updateData = (parent) => {

        const queryKey = foldersQueryKey(parent);

        const state = queryClient.getQueryState(queryKey);

        if (state == null ? void 0 : state.data) {

          queryClient.setQueryData(foldersQueryKey(parent), (folders) => folders ? folders.filter((f) => f.id !== folder2.id) : []);

        }

      };

      updateData(folder2.parent);

      updateData();

    }

  });

};

const delay = 300;

const uploadsDelayed = writable([]);

const timerMap = /* @__PURE__ */ new Map();

const uploads = {

  push(file) {

    const timer = setTimeout(() => {

      uploadsDelayed.update((files) => [file, ...files]);

    }, delay);

    timerMap.set(file, timer);

  },

  remove(file) {

    const timer = timerMap.get(file);

    if (timer !== void 0) {

      clearTimeout(timer);

      timerMap.delete(file);

    }

    uploadsDelayed.update((files) => files.filter((f) => f !== file));

  },

  subscribe: uploadsDelayed.subscribe

};

function getOptions() {

  return getContext("options");

}

function $on(el, eventNames, cb) {

  eventNames.forEach((eventName) => {

    el.addEventListener(eventName, cb);

  });

  return () => {

    eventNames.forEach((eventName) => {

      el.removeEventListener(eventName, cb);

    });

  };

}

function dragOver(node) {

  const offPreventListeners = $on(node, [

    "drag",

    "dragstart",

    "dragend",

    "dragover",

    "dragenter",

    "dragleave",

    "drop"

  ], function(e) {

    e.preventDefault();

    e.stopPropagation();

  });

  const offOver = $on(node, ["dragover", "dragenter"], function() {

    node.dispatchEvent(new CustomEvent("dropzoneover"));

  });

  const offLeave = $on(node, ["dragleave", "dragend", "drop"], function() {

    node.dispatchEvent(new CustomEvent("dropzoneleave"));

  });

  return {

    destroy() {

      offPreventListeners();

      offOver();

      offLeave();

    }

  };

}

function create_fragment$n(ctx) {

  let svg;

  let path0;

  let path1;

  return {

    c() {

      svg = svg_element("svg");

      path0 = svg_element("path");

      path1 = svg_element("path");

      attr(path0, "stroke", "currentColor");

      attr(path0, "stroke-linecap", "round");

      attr(path0, "stroke-width", "2");

      attr(path0, "d", "M12 12H8m4-4v4-4Zm0 4v4-4Zm0 0h4-4Z");

      attr(path1, "stroke", "currentColor");

      attr(path1, "stroke-width", "2");

      attr(path1, "d", "M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z");

      attr(svg, "xmlns", "http://www.w3.org/2000/svg");

      attr(svg, "fill", "none");

      attr(svg, "width", ctx[0]);

      attr(svg, "height", ctx[0]);

      attr(svg, "viewBox", "0 0 24 24");

    },

    m(target, anchor) {

      insert(target, svg, anchor);

      append(svg, path0);

      append(svg, path1);

    },

    p(ctx2, [dirty]) {

      if (dirty & 1) {

        attr(svg, "width", ctx2[0]);

      }

      if (dirty & 1) {

        attr(svg, "height", ctx2[0]);

      }

    },

    i: noop,

    o: noop,

    d(detaching) {

      if (detaching)

        detach(svg);

    }

  };

}

function instance$l($$self, $$props, $$invalidate) {

  let { size = 24 } = $$props;

  $$self.$$set = ($$props2) => {

    if ("size" in $$props2)

      $$invalidate(0, size = $$props2.size);

  };

  return [size];

}

class IconCirclePlus extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$l, create_fragment$n, safe_not_equal, { size: 0 });

  }

}

function autofocus(node) {

  node.focus();

  return {};

}

function create_fragment$m(ctx) {

  let svg;

  let path;

  return {

    c() {

      svg = svg_element("svg");

      path = svg_element("path");

      attr(path, "fill", "currentColor");

      attr(path, "fill-rule", "evenodd");

      attr(path, "d", "M0 8a1 1 0 0 1 1-1h11.58L8.3 2.7a1 1 0 1 1 1.42-1.4l6 6a1 1 0 0 1 0 1.4l-6 6a1 1 0 0 1-1.42-1.4L12.6 9H1a1 1 0 0 1-1-1Z");

      attr(path, "clip-rule", "evenodd");

      attr(svg, "xmlns", "http://www.w3.org/2000/svg");

      attr(svg, "fill", "none");

      attr(svg, "viewBox", "0 0 16 16");

      attr(svg, "width", ctx[0]);

      attr(svg, "height", ctx[0]);

    },

    m(target, anchor) {

      insert(target, svg, anchor);

      append(svg, path);

    },

    p(ctx2, [dirty]) {

      if (dirty & 1) {

        attr(svg, "width", ctx2[0]);

      }

      if (dirty & 1) {

        attr(svg, "height", ctx2[0]);

      }

    },

    i: noop,

    o: noop,

    d(detaching) {

      if (detaching)

        detach(svg);

    }

  };

}

function instance$k($$self, $$props, $$invalidate) {

  let { size = 16 } = $$props;

  $$self.$$set = ($$props2) => {

    if ("size" in $$props2)

      $$invalidate(0, size = $$props2.size);

  };

  return [size];

}

class IconArrowRight extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$k, create_fragment$m, safe_not_equal, { size: 0 });

  }

}

var IconButton_svelte_svelte_type_style_lang = "";

function create_fragment$l(ctx) {

  let button;

  let current;

  const default_slot_template = ctx[2].default;

  const default_slot = create_slot(default_slot_template, ctx, ctx[1], null);

  let button_levels = [ctx[0]];

  let button_data = {};

  for (let i = 0; i < button_levels.length; i += 1) {

    button_data = assign(button_data, button_levels[i]);

  }

  return {

    c() {

      button = element("button");

      if (default_slot)

        default_slot.c();

      set_attributes(button, button_data);

      toggle_class(button, "svelte-ms51de", true);

    },

    m(target, anchor) {

      insert(target, button, anchor);

      if (default_slot) {

        default_slot.m(button, null);

      }

      if (button.autofocus)

        button.focus();

      current = true;

    },

    p(ctx2, [dirty]) {

      if (default_slot) {

        if (default_slot.p && (!current || dirty & 2)) {

          update_slot_base(default_slot, default_slot_template, ctx2, ctx2[1], !current ? get_all_dirty_from_scope(ctx2[1]) : get_slot_changes(default_slot_template, ctx2[1], dirty, null), null);

        }

      }

      set_attributes(button, button_data = get_spread_update(button_levels, [dirty & 1 && ctx2[0]]));

      toggle_class(button, "svelte-ms51de", true);

    },

    i(local) {

      if (current)

        return;

      transition_in(default_slot, local);

      current = true;

    },

    o(local) {

      transition_out(default_slot, local);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(button);

      if (default_slot)

        default_slot.d(detaching);

    }

  };

}

function instance$j($$self, $$props, $$invalidate) {

  let { $$slots: slots = {}, $$scope } = $$props;

  $$self.$$set = ($$new_props) => {

    $$invalidate(0, $$props = assign(assign({}, $$props), exclude_internal_props($$new_props)));

    if ("$$scope" in $$new_props)

      $$invalidate(1, $$scope = $$new_props.$$scope);

  };

  $$props = exclude_internal_props($$props);

  return [$$props, $$scope, slots];

}

class IconButton extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$j, create_fragment$l, safe_not_equal, {});

  }

}

var NewFolder_svelte_svelte_type_style_lang = "";

function create_else_block$3(ctx) {

  let iconarrowright;

  let current;

  iconarrowright = new IconArrowRight({ props: { size: 12 } });

  return {

    c() {

      create_component(iconarrowright.$$.fragment);

    },

    m(target, anchor) {

      mount_component(iconarrowright, target, anchor);

      current = true;

    },

    i(local) {

      if (current)

        return;

      transition_in(iconarrowright.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconarrowright.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(iconarrowright, detaching);

    }

  };

}

function create_if_block$8(ctx) {

  let iconloader;

  let current;

  iconloader = new IconLoader({ props: { size: 12 } });

  return {

    c() {

      create_component(iconloader.$$.fragment);

    },

    m(target, anchor) {

      mount_component(iconloader, target, anchor);

      current = true;

    },

    i(local) {

      if (current)

        return;

      transition_in(iconloader.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconloader.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(iconloader, detaching);

    }

  };

}

function create_default_slot$1(ctx) {

  let current_block_type_index;

  let if_block;

  let if_block_anchor;

  let current;

  const if_block_creators = [create_if_block$8, create_else_block$3];

  const if_blocks = [];

  function select_block_type(ctx2, dirty) {

    if (ctx2[0].isLoading)

      return 0;

    return 1;

  }

  current_block_type_index = select_block_type(ctx);

  if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

  return {

    c() {

      if_block.c();

      if_block_anchor = empty();

    },

    m(target, anchor) {

      if_blocks[current_block_type_index].m(target, anchor);

      insert(target, if_block_anchor, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      let previous_block_index = current_block_type_index;

      current_block_type_index = select_block_type(ctx2);

      if (current_block_type_index !== previous_block_index) {

        group_outros();

        transition_out(if_blocks[previous_block_index], 1, 1, () => {

          if_blocks[previous_block_index] = null;

        });

        check_outros();

        if_block = if_blocks[current_block_type_index];

        if (!if_block) {

          if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx2);

          if_block.c();

        }

        transition_in(if_block, 1);

        if_block.m(if_block_anchor.parentNode, if_block_anchor);

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if_blocks[current_block_type_index].d(detaching);

      if (detaching)

        detach(if_block_anchor);

    }

  };

}

function create_fragment$k(ctx) {

  let form;

  let iconfolder;

  let t0;

  let input;

  let input_disabled_value;

  let t1;

  let iconbutton;

  let current;

  let mounted;

  let dispose;

  iconfolder = new IconFolder({});

  iconbutton = new IconButton({

    props: {

      disabled: ctx[0].isLoading,

      $$slots: { default: [create_default_slot$1] },

      $$scope: { ctx }

    }

  });

  return {

    c() {

      form = element("form");

      create_component(iconfolder.$$.fragment);

      t0 = space();

      input = element("input");

      t1 = space();

      create_component(iconbutton.$$.fragment);

      attr(input, "type", "text");

      attr(input, "placeholder", t("newFolderPlaceholder"));

      attr(input, "name", "name");

      input.required = true;

      input.disabled = input_disabled_value = ctx[0].isLoading;

      attr(input, "class", "svelte-bopbht");

      attr(form, "action", "");

      attr(form, "class", "fm-folder-form svelte-bopbht");

    },

    m(target, anchor) {

      insert(target, form, anchor);

      mount_component(iconfolder, form, null);

      append(form, t0);

      append(form, input);

      append(form, t1);

      mount_component(iconbutton, form, null);

      current = true;

      if (!mounted) {

        dispose = [

          action_destroyer(autofocus.call(null, input)),

          listen(form, "submit", prevent_default(ctx[2])),

          action_destroyer(clickOutside.call(null, form)),

          listen(form, "outclick", ctx[3])

        ];

        mounted = true;

      }

    },

    p(ctx2, [dirty]) {

      if (!current || dirty & 1 && input_disabled_value !== (input_disabled_value = ctx2[0].isLoading)) {

        input.disabled = input_disabled_value;

      }

      const iconbutton_changes = {};

      if (dirty & 1)

        iconbutton_changes.disabled = ctx2[0].isLoading;

      if (dirty & 65) {

        iconbutton_changes.$$scope = { dirty, ctx: ctx2 };

      }

      iconbutton.$set(iconbutton_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(iconfolder.$$.fragment, local);

      transition_in(iconbutton.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconfolder.$$.fragment, local);

      transition_out(iconbutton.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(form);

      destroy_component(iconfolder);

      destroy_component(iconbutton);

      mounted = false;

      run_all(dispose);

    }

  };

}

function instance$i($$self, $$props, $$invalidate) {

  let $createFolderMutation;

  let { parent } = $$props;

  const createFolderMutation = useCreateFolderMutation();

  component_subscribe($$self, createFolderMutation, (value) => $$invalidate(0, $createFolderMutation = value));

  const handleSubmit = async (e) => {

    const name = new FormData(e.currentTarget).get("name").toString();

    await $createFolderMutation.mutateAsync({ name, parent: parent == null ? void 0 : parent.id });

    dispatch2("submit");

  };

  const handleCancel = () => {

    dispatch2("cancel");

  };

  const dispatch2 = createEventDispatcher();

  $$self.$$set = ($$props2) => {

    if ("parent" in $$props2)

      $$invalidate(4, parent = $$props2.parent);

  };

  return [

    $createFolderMutation,

    createFolderMutation,

    handleSubmit,

    handleCancel,

    parent

  ];

}

class NewFolder extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$i, create_fragment$k, safe_not_equal, { parent: 4 });

  }

}

function nestFolder(originalFolders) {

  const folders = originalFolders.map((folder2) => __spreadProps(__spreadValues({}, folder2), {

    children: []

  }));

  const foldersById = folders.reduce((acc, folder2) => acc.set(folder2.id, folder2), /* @__PURE__ */ new Map());

  for (const folder2 of folders) {

    const parent = foldersById.get(folder2.parent);

    if (folder2.parent && parent) {

      parent.children = parent.children ? [...parent.children, folder2] : [folder2];

    }

  }

  return folders;

}

function tooltip(node, title) {

  let tooltip2 = null;

  const onMouveOver = () => {

    if (tooltip2) {

      return;

    }

    const rect = node.getBoundingClientRect();

    tooltip2 = document.createElement("div");

    tooltip2.classList.add("fm-tooltip");

    tooltip2.innerText = title;

    const root = node.closest(".fm-root");

    root.appendChild(tooltip2);

    tooltip2.style.setProperty("transform", `translate(calc(${rect.left + rect.width / 2}px - 50%), calc(${rect.top - 4}px - 100%))`);

    tooltip2.animate([{ opacity: 0 }, { opacity: 1 }], {

      duration: 200,

      easing: "ease-in-out"

    });

    node.addEventListener("pointerleave", () => {

      if (tooltip2) {

        tooltip2.animate([{ opacity: 1 }, { opacity: 0 }], {

          duration: 200,

          easing: "ease-in-out"

        });

        window.setTimeout(() => {

          tooltip2 == null ? void 0 : tooltip2.remove();

          tooltip2 = null;

        }, 200);

      }

    }, { once: true });

  };

  node.addEventListener("pointerenter", onMouveOver);

  return {

    destroy() {

      tooltip2 == null ? void 0 : tooltip2.remove();

      node.removeEventListener("pointerenter", onMouveOver);

    }

  };

}

var Folder_svelte_svelte_type_style_lang = "";

function create_else_block$2(ctx) {

  let iconfolder;

  let current;

  iconfolder = new IconFolder({ props: { class: "folder-icon" } });

  return {

    c() {

      create_component(iconfolder.$$.fragment);

    },

    m(target, anchor) {

      mount_component(iconfolder, target, anchor);

      current = true;

    },

    i(local) {

      if (current)

        return;

      transition_in(iconfolder.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconfolder.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(iconfolder, detaching);

    }

  };

}

function create_if_block_4$1(ctx) {

  let iconloader;

  let current;

  iconloader = new IconLoader({

    props: { size: 20, class: "folder-loader" }

  });

  return {

    c() {

      create_component(iconloader.$$.fragment);

    },

    m(target, anchor) {

      mount_component(iconloader, target, anchor);

      current = true;

    },

    i(local) {

      if (current)

        return;

      transition_in(iconloader.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconloader.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(iconloader, detaching);

    }

  };

}

function create_if_block_3$1(ctx) {

  let button;

  let iconcircleplus;

  let tooltip_action;

  let current;

  let mounted;

  let dispose;

  iconcircleplus = new IconCirclePlus({ props: { size: 16 } });

  return {

    c() {

      button = element("button");

      create_component(iconcircleplus.$$.fragment);

      attr(button, "class", "fm-new-folder svelte-1nyuu78");

    },

    m(target, anchor) {

      insert(target, button, anchor);

      mount_component(iconcircleplus, button, null);

      current = true;

      if (!mounted) {

        dispose = [

          listen(button, "click", prevent_default(ctx[12])),

          action_destroyer(tooltip_action = tooltip.call(null, button, t("createFolder")))

        ];

        mounted = true;

      }

    },

    p: noop,

    i(local) {

      if (current)

        return;

      transition_in(iconcircleplus.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconcircleplus.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(button);

      destroy_component(iconcircleplus);

      mounted = false;

      run_all(dispose);

    }

  };

}

function create_if_block_2$1(ctx) {

  let newfolder;

  let current;

  newfolder = new NewFolder({ props: { parent: ctx[0] } });

  newfolder.$on("submit", ctx[13]);

  newfolder.$on("cancel", ctx[13]);

  return {

    c() {

      create_component(newfolder.$$.fragment);

    },

    m(target, anchor) {

      mount_component(newfolder, target, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      const newfolder_changes = {};

      if (dirty & 1)

        newfolder_changes.parent = ctx2[0];

      newfolder.$set(newfolder_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(newfolder.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(newfolder.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(newfolder, detaching);

    }

  };

}

function create_if_block_1$2(ctx) {

  let folders;

  let current;

  folders = new Folders({

    props: {

      folders: ctx[6],

      lazyLoad: ctx[1]

    }

  });

  return {

    c() {

      create_component(folders.$$.fragment);

    },

    m(target, anchor) {

      mount_component(folders, target, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      const folders_changes = {};

      if (dirty & 64)

        folders_changes.folders = ctx2[6];

      if (dirty & 2)

        folders_changes.lazyLoad = ctx2[1];

      folders.$set(folders_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(folders.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(folders.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(folders, detaching);

    }

  };

}

function create_if_block$7(ctx) {

  var _a;

  let folders;

  let current;

  folders = new Folders({

    props: {

      folders: (_a = ctx[0]) == null ? void 0 : _a.children,

      lazyLoad: ctx[1]

    }

  });

  return {

    c() {

      create_component(folders.$$.fragment);

    },

    m(target, anchor) {

      mount_component(folders, target, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      var _a2;

      const folders_changes = {};

      if (dirty & 1)

        folders_changes.folders = (_a2 = ctx2[0]) == null ? void 0 : _a2.children;

      if (dirty & 2)

        folders_changes.lazyLoad = ctx2[1];

      folders.$set(folders_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(folders.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(folders.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(folders, detaching);

    }

  };

}

function create_fragment$j(ctx) {

  var _a, _b;

  let li;

  let span2;

  let span1;

  let current_block_type_index;

  let if_block0;

  let t0;

  let span0;

  let t1_value = ((_b = (_a = ctx[0]) == null ? void 0 : _a.name) != null ? _b : "/") + "";

  let t1;

  let t2;

  let t3;

  let t4;

  let current_block_type_index_1;

  let if_block3;

  let current;

  let mounted;

  let dispose;

  const if_block_creators = [create_if_block_4$1, create_else_block$2];

  const if_blocks = [];

  function select_block_type(ctx2, dirty) {

    if (ctx2[2].isLoading)

      return 0;

    return 1;

  }

  current_block_type_index = select_block_type(ctx);

  if_block0 = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

  let if_block1 = !ctx[8].readOnly && create_if_block_3$1(ctx);

  let if_block2 = ctx[4] && create_if_block_2$1(ctx);

  const if_block_creators_1 = [create_if_block$7, create_if_block_1$2];

  const if_blocks_1 = [];

  function select_block_type_1(ctx2, dirty) {

    var _a2;

    if (((_a2 = ctx2[0]) == null ? void 0 : _a2.children) && ctx2[5])

      return 0;

    if (ctx2[6] && ctx2[5])

      return 1;

    return -1;

  }

  if (~(current_block_type_index_1 = select_block_type_1(ctx))) {

    if_block3 = if_blocks_1[current_block_type_index_1] = if_block_creators_1[current_block_type_index_1](ctx);

  }

  return {

    c() {

      var _a2, _b2;

      li = element("li");

      span2 = element("span");

      span1 = element("span");

      if_block0.c();

      t0 = space();

      span0 = element("span");

      t1 = text(t1_value);

      t2 = space();

      if (if_block1)

        if_block1.c();

      t3 = space();

      if (if_block2)

        if_block2.c();

      t4 = space();

      if (if_block3)

        if_block3.c();

      attr(span0, "class", "fm-folder-name svelte-1nyuu78");

      attr(span1, "class", "fm-folder svelte-1nyuu78");

      attr(span2, "class", "fm-folder-wrapper svelte-1nyuu78");

      toggle_class(span2, "active", ((_a2 = ctx[0]) == null ? void 0 : _a2.id) === ((_b2 = ctx[7]) == null ? void 0 : _b2.id) || ctx[3]);

    },

    m(target, anchor) {

      insert(target, li, anchor);

      append(li, span2);

      append(span2, span1);

      if_blocks[current_block_type_index].m(span1, null);

      append(span1, t0);

      append(span1, span0);

      append(span0, t1);

      append(span2, t2);

      if (if_block1)

        if_block1.m(span2, null);

      append(li, t3);

      if (if_block2)

        if_block2.m(li, null);

      append(li, t4);

      if (~current_block_type_index_1) {

        if_blocks_1[current_block_type_index_1].m(li, null);

      }

      current = true;

      if (!mounted) {

        dispose = [

          action_destroyer(dragOver.call(null, span1)),

          listen(span1, "click", prevent_default(ctx[14])),

          listen(span1, "dropzoneover", ctx[9]),

          listen(span1, "dropzoneleave", ctx[10]),

          listen(span1, "drop", ctx[11])

        ];

        mounted = true;

      }

    },

    p(ctx2, [dirty]) {

      var _a2, _b2, _c, _d;

      let previous_block_index = current_block_type_index;

      current_block_type_index = select_block_type(ctx2);

      if (current_block_type_index !== previous_block_index) {

        group_outros();

        transition_out(if_blocks[previous_block_index], 1, 1, () => {

          if_blocks[previous_block_index] = null;

        });

        check_outros();

        if_block0 = if_blocks[current_block_type_index];

        if (!if_block0) {

          if_block0 = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx2);

          if_block0.c();

        }

        transition_in(if_block0, 1);

        if_block0.m(span1, t0);

      }

      if ((!current || dirty & 1) && t1_value !== (t1_value = ((_b2 = (_a2 = ctx2[0]) == null ? void 0 : _a2.name) != null ? _b2 : "/") + ""))

        set_data(t1, t1_value);

      if (!ctx2[8].readOnly)

        if_block1.p(ctx2, dirty);

      if (dirty & 137) {

        toggle_class(span2, "active", ((_c = ctx2[0]) == null ? void 0 : _c.id) === ((_d = ctx2[7]) == null ? void 0 : _d.id) || ctx2[3]);

      }

      if (ctx2[4]) {

        if (if_block2) {

          if_block2.p(ctx2, dirty);

          if (dirty & 16) {

            transition_in(if_block2, 1);

          }

        } else {

          if_block2 = create_if_block_2$1(ctx2);

          if_block2.c();

          transition_in(if_block2, 1);

          if_block2.m(li, t4);

        }

      } else if (if_block2) {

        group_outros();

        transition_out(if_block2, 1, 1, () => {

          if_block2 = null;

        });

        check_outros();

      }

      let previous_block_index_1 = current_block_type_index_1;

      current_block_type_index_1 = select_block_type_1(ctx2);

      if (current_block_type_index_1 === previous_block_index_1) {

        if (~current_block_type_index_1) {

          if_blocks_1[current_block_type_index_1].p(ctx2, dirty);

        }

      } else {

        if (if_block3) {

          group_outros();

          transition_out(if_blocks_1[previous_block_index_1], 1, 1, () => {

            if_blocks_1[previous_block_index_1] = null;

          });

          check_outros();

        }

        if (~current_block_type_index_1) {

          if_block3 = if_blocks_1[current_block_type_index_1];

          if (!if_block3) {

            if_block3 = if_blocks_1[current_block_type_index_1] = if_block_creators_1[current_block_type_index_1](ctx2);

            if_block3.c();

          } else {

            if_block3.p(ctx2, dirty);

          }

          transition_in(if_block3, 1);

          if_block3.m(li, null);

        } else {

          if_block3 = null;

        }

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block0);

      transition_in(if_block1);

      transition_in(if_block2);

      transition_in(if_block3);

      current = true;

    },

    o(local) {

      transition_out(if_block0);

      transition_out(if_block1);

      transition_out(if_block2);

      transition_out(if_block3);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(li);

      if_blocks[current_block_type_index].d();

      if (if_block1)

        if_block1.d();

      if (if_block2)

        if_block2.d();

      if (~current_block_type_index_1) {

        if_blocks_1[current_block_type_index_1].d();

      }

      mounted = false;

      run_all(dispose);

    }

  };

}

function instance$h($$self, $$props, $$invalidate) {

  let $childrenQuery;

  let $currentFolder;

  component_subscribe($$self, folder, ($$value) => $$invalidate(7, $currentFolder = $$value));

  const queryClient = useQueryClient();

  let { folder: folder$1 } = $$props;

  let { lazyLoad } = $$props;

  let over = false;

  let addNewFolder = false;

  let showChildren = !(folder$1 == null ? void 0 : folder$1.id);

  const options = getOptions();

  const handleDragOver = () => {

    if (!options.readOnly) {

      $$invalidate(3, over = true);

    }

  };

  const handleDragLeave = () => {

    if (!options.readOnly) {

      $$invalidate(3, over = false);

    }

  };

  const handleDrop = (e) => {

    if (options.readOnly) {

      e.preventDefault();

    }

    Array.from(e.dataTransfer.files).forEach((file) => uploadFile(options, queryClient, file, folder$1));

  };

  const handleAddFolder = () => {

    $$invalidate(4, addNewFolder = true);

    $$invalidate(5, showChildren = true);

    if (!$childrenQuery.isSuccess && (folder$1 == null ? void 0 : folder$1.children) === void 0) {

      $childrenQuery.refetch();

    }

  };

  const exitAddFolder = () => {

    $$invalidate(4, addNewFolder = false);

  };

  const loadChildren = () => {

    if (showChildren && $currentFolder === folder$1) {

      $$invalidate(5, showChildren = false);

      return;

    }

    $$invalidate(5, showChildren = true);

    set_store_value(folder, $currentFolder = folder$1, $currentFolder);

    if ((folder$1 == null ? void 0 : folder$1.children) === void 0) {

      $childrenQuery.refetch();

    }

  };

  const childrenQuery = useQuery(foldersQueryKey(folder$1 == null ? void 0 : folder$1.id), () => options.getFolders((folder$1 == null ? void 0 : folder$1.id) ? folder$1 : void 0), { enabled: !(folder$1 == null ? void 0 : folder$1.id) });

  component_subscribe($$self, childrenQuery, (value) => $$invalidate(2, $childrenQuery = value));

  let children2 = null;

  $$self.$$set = ($$props2) => {

    if ("folder" in $$props2)

      $$invalidate(0, folder$1 = $$props2.folder);

    if ("lazyLoad" in $$props2)

      $$invalidate(1, lazyLoad = $$props2.lazyLoad);

  };

  $$self.$$.update = () => {

    if ($$self.$$.dirty & 7) {

      {

        if ($childrenQuery.isSuccess) {

          $$invalidate(6, children2 = (lazyLoad ? $childrenQuery.data : nestFolder($childrenQuery.data)).filter((f) => {

            var _a, _b;

            return ((_a = f.parent) != null ? _a : null) === ((_b = folder$1 == null ? void 0 : folder$1.id) != null ? _b : null);

          }));

        }

      }

    }

  };

  return [

    folder$1,

    lazyLoad,

    $childrenQuery,

    over,

    addNewFolder,

    showChildren,

    children2,

    $currentFolder,

    options,

    handleDragOver,

    handleDragLeave,

    handleDrop,

    handleAddFolder,

    exitAddFolder,

    loadChildren,

    childrenQuery

  ];

}

class Folder extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$h, create_fragment$j, safe_not_equal, { folder: 0, lazyLoad: 1 });

  }

}

var Folders_svelte_svelte_type_style_lang = "";

function get_each_context$4(ctx, list, i) {

  const child_ctx = ctx.slice();

  child_ctx[2] = list[i];

  return child_ctx;

}

function create_each_block$4(ctx) {

  let folder2;

  let current;

  folder2 = new Folder({

    props: {

      folder: ctx[2],

      lazyLoad: ctx[1]

    }

  });

  return {

    c() {

      create_component(folder2.$$.fragment);

    },

    m(target, anchor) {

      mount_component(folder2, target, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      const folder_changes = {};

      if (dirty & 1)

        folder_changes.folder = ctx2[2];

      if (dirty & 2)

        folder_changes.lazyLoad = ctx2[1];

      folder2.$set(folder_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(folder2.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(folder2.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(folder2, detaching);

    }

  };

}

function create_fragment$i(ctx) {

  let ul;

  let current;

  let each_value = ctx[0];

  let each_blocks = [];

  for (let i = 0; i < each_value.length; i += 1) {

    each_blocks[i] = create_each_block$4(get_each_context$4(ctx, each_value, i));

  }

  const out = (i) => transition_out(each_blocks[i], 1, 1, () => {

    each_blocks[i] = null;

  });

  return {

    c() {

      ul = element("ul");

      for (let i = 0; i < each_blocks.length; i += 1) {

        each_blocks[i].c();

      }

      attr(ul, "class", "fm-folders svelte-1spuanm");

    },

    m(target, anchor) {

      insert(target, ul, anchor);

      for (let i = 0; i < each_blocks.length; i += 1) {

        each_blocks[i].m(ul, null);

      }

      current = true;

    },

    p(ctx2, [dirty]) {

      if (dirty & 3) {

        each_value = ctx2[0];

        let i;

        for (i = 0; i < each_value.length; i += 1) {

          const child_ctx = get_each_context$4(ctx2, each_value, i);

          if (each_blocks[i]) {

            each_blocks[i].p(child_ctx, dirty);

            transition_in(each_blocks[i], 1);

          } else {

            each_blocks[i] = create_each_block$4(child_ctx);

            each_blocks[i].c();

            transition_in(each_blocks[i], 1);

            each_blocks[i].m(ul, null);

          }

        }

        group_outros();

        for (i = each_value.length; i < each_blocks.length; i += 1) {

          out(i);

        }

        check_outros();

      }

    },

    i(local) {

      if (current)

        return;

      for (let i = 0; i < each_value.length; i += 1) {

        transition_in(each_blocks[i]);

      }

      current = true;

    },

    o(local) {

      each_blocks = each_blocks.filter(Boolean);

      for (let i = 0; i < each_blocks.length; i += 1) {

        transition_out(each_blocks[i]);

      }

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(ul);

      destroy_each(each_blocks, detaching);

    }

  };

}

function instance$g($$self, $$props, $$invalidate) {

  let { folders } = $$props;

  let { lazyLoad } = $$props;

  $$self.$$set = ($$props2) => {

    if ("folders" in $$props2)

      $$invalidate(0, folders = $$props2.folders);

    if ("lazyLoad" in $$props2)

      $$invalidate(1, lazyLoad = $$props2.lazyLoad);

  };

  return [folders, lazyLoad];

}

class Folders extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$g, create_fragment$i, safe_not_equal, { folders: 0, lazyLoad: 1 });

  }

}

function create_fragment$h(ctx) {

  let svg;

  let path0;

  let path1;

  return {

    c() {

      svg = svg_element("svg");

      path0 = svg_element("path");

      path1 = svg_element("path");

      attr(path0, "d", "M0 6.417a6.424 6.424 0 006.417 6.416 6.424 6.424 0 006.416-6.416A6.424 6.424 0 006.417 0 6.424 6.424 0 000 6.417zm1.833 0a4.589 4.589 0 014.584-4.584A4.589 4.589 0 0111 6.417 4.589 4.589 0 016.417 11a4.589 4.589 0 01-4.584-4.583z");

      attr(path0, "fill", "currentColor");

      attr(path1, "d", "M13.75 12.543L11.707 10.5c-.35.452-.755.856-1.207 1.207l2.043 2.043a.851.851 0 001.207 0 .853.853 0 000-1.207z");

      attr(path1, "fill", "currentColor");

      attr(svg, "width", "14");

      attr(svg, "height", "14");

      attr(svg, "fill", "none");

      attr(svg, "xmlns", "http://www.w3.org/2000/svg");

    },

    m(target, anchor) {

      insert(target, svg, anchor);

      append(svg, path0);

      append(svg, path1);

    },

    p: noop,

    i: noop,

    o: noop,

    d(detaching) {

      if (detaching)

        detach(svg);

    }

  };

}

class IconSearch extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, null, create_fragment$h, safe_not_equal, {});

  }

}

var Search_svelte_svelte_type_style_lang = "";

function create_fragment$g(ctx) {

  let form;

  let input;

  let t2;

  let button;

  let iconsearch;

  let current;

  let mounted;

  let dispose;

  iconsearch = new IconSearch({});

  return {

    c() {

      form = element("form");

      input = element("input");

      t2 = space();

      button = element("button");

      create_component(iconsearch.$$.fragment);

      attr(input, "type", "search");

      attr(input, "name", "search");

      attr(input, "placeholder", "e.g. image.png");

      attr(input, "class", "svelte-15kvubs");

      attr(button, "title", "Search");

      attr(button, "class", "svelte-15kvubs");

      attr(form, "class", "search svelte-15kvubs");

    },

    m(target, anchor) {

      insert(target, form, anchor);

      append(form, input);

      set_input_value(input, ctx[0]);

      append(form, t2);

      append(form, button);

      mount_component(iconsearch, button, null);

      current = true;

      if (!mounted) {

        dispose = listen(input, "input", ctx[1]);

        mounted = true;

      }

    },

    p(ctx2, [dirty]) {

      if (dirty & 1) {

        set_input_value(input, ctx2[0]);

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(iconsearch.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconsearch.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(form);

      destroy_component(iconsearch);

      mounted = false;

      dispose();

    }

  };

}

function instance$f($$self, $$props, $$invalidate) {

  let $searchQuery;

  component_subscribe($$self, searchQuery, ($$value) => $$invalidate(0, $searchQuery = $$value));

  folder.subscribe(() => {

    set_store_value(searchQuery, $searchQuery = "", $searchQuery);

  });

  function input_input_handler() {

    $searchQuery = this.value;

    searchQuery.set($searchQuery);

  }

  return [$searchQuery, input_input_handler];

}

class Search extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$f, create_fragment$g, safe_not_equal, {});

  }

}

var Sidebar_svelte_svelte_type_style_lang = "";

function create_fragment$f(ctx) {

  let aside;

  let search;

  let t2;

  let folders;

  let current;

  search = new Search({});

  folders = new Folders({

    props: {

      folders: [null],

      lazyLoad: ctx[0]

    }

  });

  return {

    c() {

      aside = element("aside");

      create_component(search.$$.fragment);

      t2 = space();

      create_component(folders.$$.fragment);

      attr(aside, "class", "fm-sidebar svelte-jz8ywq");

    },

    m(target, anchor) {

      insert(target, aside, anchor);

      mount_component(search, aside, null);

      append(aside, t2);

      mount_component(folders, aside, null);

      current = true;

    },

    p(ctx2, [dirty]) {

      const folders_changes = {};

      if (dirty & 1)

        folders_changes.lazyLoad = ctx2[0];

      folders.$set(folders_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(search.$$.fragment, local);

      transition_in(folders.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(search.$$.fragment, local);

      transition_out(folders.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(aside);

      destroy_component(search);

      destroy_component(folders);

    }

  };

}

function instance$e($$self, $$props, $$invalidate) {

  let { lazyFolders } = $$props;

  $$self.$$set = ($$props2) => {

    if ("lazyFolders" in $$props2)

      $$invalidate(0, lazyFolders = $$props2.lazyFolders);

  };

  return [lazyFolders];

}

class Sidebar extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$e, create_fragment$f, safe_not_equal, { lazyFolders: 0 });

  }

}

var IconUpload_svelte_svelte_type_style_lang = "";

function create_fragment$e(ctx) {

  let svg;

  let path0;

  let path1;

  let path1_class_value;

  return {

    c() {

      svg = svg_element("svg");

      path0 = svg_element("path");

      path1 = svg_element("path");

      attr(path0, "d", "M21.12 21.1765L3.83997 21.1765L3.83997 19.7647C3.83997 18.985 3.19526 18.3529 2.39997 18.3529C1.60468 18.3529 0.959966 18.985 0.959966 19.7647L0.959966 22.5882C0.959966 22.9781 1.12114 23.331 1.38173 23.5865C1.64232 23.842 2.00232 24 2.39997 24L22.56 24C22.7541 24 22.9393 23.9623 23.1084 23.894C23.6317 23.6826 24 23.1776 24 22.5882L24 19.7647C24 18.985 23.3553 18.3529 22.56 18.3529C21.7647 18.3529 21.12 18.985 21.12 19.7647L21.12 21.1765Z");

      attr(path0, "fill", "currentColor");

      attr(path1, "d", "M18.884 10.0314C18.6841 9.93335 18.4636 9.88232 18.2401 9.88232C17.9285 9.88232 17.6253 9.9814 17.3761 10.1647L13.92 12.7159C13.92 12.7125 13.92 12.7092 13.92 12.7059L13.92 1.41176C13.92 1.03734 13.7683 0.67825 13.4983 0.413493C13.2282 0.148736 12.862 -2.98024e-06 12.48 -3.01363e-06C12.0981 -3.04702e-06 11.7319 0.148736 11.4618 0.413493C11.1918 0.67825 11.04 1.03734 11.04 1.41176L11.04 12.7059C11.04 12.7325 11.0408 12.7591 11.0423 12.7855L7.55526 10.3764C7.40048 10.2681 7.22535 10.1909 7.03998 10.1493C6.85461 10.1077 6.66267 10.1025 6.47523 10.134C6.2878 10.1655 6.10858 10.233 5.94794 10.3328C5.78729 10.4326 5.64839 10.5626 5.53926 10.7153C5.32063 11.0207 5.23426 11.3986 5.29902 11.7664C5.36378 12.1341 5.57441 12.4617 5.88485 12.6776L11.6449 16.6588C11.8915 16.8355 12.189 16.9307 12.4945 16.9307C12.7999 16.9307 13.0974 16.8355 13.3441 16.6588L19.1041 12.4235C19.2553 12.3123 19.3828 12.1729 19.4791 12.0134C19.5755 11.8538 19.6388 11.6773 19.6656 11.4937C19.6923 11.3102 19.6819 11.1233 19.635 10.9437C19.5881 10.7641 19.5055 10.5953 19.3921 10.447C19.2579 10.2717 19.084 10.1294 18.884 10.0314Z");

      attr(path1, "fill", "currentColor");

      attr(path1, "class", path1_class_value = null_to_empty(ctx[0] ? "animatedArrow" : null) + " svelte-1k824dx");

      attr(svg, "width", "24");

      attr(svg, "height", "24");

      attr(svg, "viewBox", "0 0 24 24");

      attr(svg, "fill", "none");

      attr(svg, "xmlns", "http://www.w3.org/2000/svg");

    },

    m(target, anchor) {

      insert(target, svg, anchor);

      append(svg, path0);

      append(svg, path1);

    },

    p(ctx2, [dirty]) {

      if (dirty & 1 && path1_class_value !== (path1_class_value = null_to_empty(ctx2[0] ? "animatedArrow" : null) + " svelte-1k824dx")) {

        attr(path1, "class", path1_class_value);

      }

    },

    i: noop,

    o: noop,

    d(detaching) {

      if (detaching)

        detach(svg);

    }

  };

}

function instance$d($$self, $$props, $$invalidate) {

  let { animated } = $$props;

  $$self.$$set = ($$props2) => {

    if ("animated" in $$props2)

      $$invalidate(0, animated = $$props2.animated);

  };

  return [animated];

}

class IconUpload extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$d, create_fragment$e, safe_not_equal, { animated: 0 });

  }

}

var Dropzone_svelte_svelte_type_style_lang = "";

function create_else_block$1(ctx) {

  let main;

  let t2;

  let span;

  let iconupload;

  let current;

  let mounted;

  let dispose;

  const default_slot_template = ctx[6].default;

  const default_slot = create_slot(default_slot_template, ctx, ctx[5], null);

  iconupload = new IconUpload({ props: { animated: ctx[0] } });

  return {

    c() {

      main = element("main");

      if (default_slot)

        default_slot.c();

      t2 = space();

      span = element("span");

      create_component(iconupload.$$.fragment);

      attr(span, "class", "fm-dropzone svelte-lkheja");

      toggle_class(span, "active", ctx[0]);

      attr(main, "class", "fm-main svelte-lkheja");

    },

    m(target, anchor) {

      insert(target, main, anchor);

      if (default_slot) {

        default_slot.m(main, null);

      }

      append(main, t2);

      append(main, span);

      mount_component(iconupload, span, null);

      current = true;

      if (!mounted) {

        dispose = [

          action_destroyer(dragOver.call(null, main)),

          listen(main, "dropzoneover", ctx[1]),

          listen(main, "dropzoneleave", ctx[2]),

          listen(main, "drop", ctx[4])

        ];

        mounted = true;

      }

    },

    p(ctx2, dirty) {

      if (default_slot) {

        if (default_slot.p && (!current || dirty & 32)) {

          update_slot_base(default_slot, default_slot_template, ctx2, ctx2[5], !current ? get_all_dirty_from_scope(ctx2[5]) : get_slot_changes(default_slot_template, ctx2[5], dirty, null), null);

        }

      }

      const iconupload_changes = {};

      if (dirty & 1)

        iconupload_changes.animated = ctx2[0];

      iconupload.$set(iconupload_changes);

      if (dirty & 1) {

        toggle_class(span, "active", ctx2[0]);

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(default_slot, local);

      transition_in(iconupload.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(default_slot, local);

      transition_out(iconupload.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(main);

      if (default_slot)

        default_slot.d(detaching);

      destroy_component(iconupload);

      mounted = false;

      run_all(dispose);

    }

  };

}

function create_if_block$6(ctx) {

  let main;

  let current;

  const default_slot_template = ctx[6].default;

  const default_slot = create_slot(default_slot_template, ctx, ctx[5], null);

  return {

    c() {

      main = element("main");

      if (default_slot)

        default_slot.c();

      attr(main, "class", "fm-main svelte-lkheja");

    },

    m(target, anchor) {

      insert(target, main, anchor);

      if (default_slot) {

        default_slot.m(main, null);

      }

      current = true;

    },

    p(ctx2, dirty) {

      if (default_slot) {

        if (default_slot.p && (!current || dirty & 32)) {

          update_slot_base(default_slot, default_slot_template, ctx2, ctx2[5], !current ? get_all_dirty_from_scope(ctx2[5]) : get_slot_changes(default_slot_template, ctx2[5], dirty, null), null);

        }

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(default_slot, local);

      current = true;

    },

    o(local) {

      transition_out(default_slot, local);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(main);

      if (default_slot)

        default_slot.d(detaching);

    }

  };

}

function create_fragment$d(ctx) {

  let current_block_type_index;

  let if_block;

  let if_block_anchor;

  let current;

  const if_block_creators = [create_if_block$6, create_else_block$1];

  const if_blocks = [];

  function select_block_type(ctx2, dirty) {

    if (ctx2[3].readOnly)

      return 0;

    return 1;

  }

  current_block_type_index = select_block_type(ctx);

  if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

  return {

    c() {

      if_block.c();

      if_block_anchor = empty();

    },

    m(target, anchor) {

      if_blocks[current_block_type_index].m(target, anchor);

      insert(target, if_block_anchor, anchor);

      current = true;

    },

    p(ctx2, [dirty]) {

      if_block.p(ctx2, dirty);

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if_blocks[current_block_type_index].d(detaching);

      if (detaching)

        detach(if_block_anchor);

    }

  };

}

function instance$c($$self, $$props, $$invalidate) {

  let $folder;

  component_subscribe($$self, folder, ($$value) => $$invalidate(7, $folder = $$value));

  let { $$slots: slots = {}, $$scope } = $$props;

  let over = false;

  const handleDragOver = () => $$invalidate(0, over = true);

  const handleDragLeave = () => $$invalidate(0, over = false);

  const queryClient = useQueryClient();

  const options = getOptions();

  const handleDrop = (e) => {

    Array.from(e.dataTransfer.files).forEach(async (file) => {

      uploads.push(file);

      await uploadFile(options, queryClient, file, $folder);

      uploads.remove(file);

    });

  };

  $$self.$$set = ($$props2) => {

    if ("$$scope" in $$props2)

      $$invalidate(5, $$scope = $$props2.$$scope);

  };

  return [over, handleDragOver, handleDragLeave, options, handleDrop, $$scope, slots];

}

class Dropzone extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$c, create_fragment$d, safe_not_equal, {});

  }

}

var UploadProgress_svelte_svelte_type_style_lang = "";

function get_each_context$3(ctx, list, i) {

  const child_ctx = ctx.slice();

  child_ctx[1] = list[i];

  return child_ctx;

}

function create_if_block$5(ctx) {

  let aside;

  let current;

  let each_value = ctx[0];

  let each_blocks = [];

  for (let i = 0; i < each_value.length; i += 1) {

    each_blocks[i] = create_each_block$3(get_each_context$3(ctx, each_value, i));

  }

  const out = (i) => transition_out(each_blocks[i], 1, 1, () => {

    each_blocks[i] = null;

  });

  return {

    c() {

      aside = element("aside");

      for (let i = 0; i < each_blocks.length; i += 1) {

        each_blocks[i].c();

      }

      attr(aside, "class", "fm-upload-progress svelte-3nncjb");

    },

    m(target, anchor) {

      insert(target, aside, anchor);

      for (let i = 0; i < each_blocks.length; i += 1) {

        each_blocks[i].m(aside, null);

      }

      current = true;

    },

    p(ctx2, dirty) {

      if (dirty & 1) {

        each_value = ctx2[0];

        let i;

        for (i = 0; i < each_value.length; i += 1) {

          const child_ctx = get_each_context$3(ctx2, each_value, i);

          if (each_blocks[i]) {

            each_blocks[i].p(child_ctx, dirty);

            transition_in(each_blocks[i], 1);

          } else {

            each_blocks[i] = create_each_block$3(child_ctx);

            each_blocks[i].c();

            transition_in(each_blocks[i], 1);

            each_blocks[i].m(aside, null);

          }

        }

        group_outros();

        for (i = each_value.length; i < each_blocks.length; i += 1) {

          out(i);

        }

        check_outros();

      }

    },

    i(local) {

      if (current)

        return;

      for (let i = 0; i < each_value.length; i += 1) {

        transition_in(each_blocks[i]);

      }

      current = true;

    },

    o(local) {

      each_blocks = each_blocks.filter(Boolean);

      for (let i = 0; i < each_blocks.length; i += 1) {

        transition_out(each_blocks[i]);

      }

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(aside);

      destroy_each(each_blocks, detaching);

    }

  };

}

function create_each_block$3(ctx) {

  let div2;

  let div1;

  let t0_value = ctx[1].name + "";

  let t0;

  let t1;

  let div0;

  let t2;

  let div2_transition;

  let current;

  return {

    c() {

      div2 = element("div");

      div1 = element("div");

      t0 = text(t0_value);

      t1 = space();

      div0 = element("div");

      t2 = space();

      attr(div0, "class", "fm-upload-progress-bar svelte-3nncjb");

      attr(div1, "class", "fm-upload-progress-item svelte-3nncjb");

    },

    m(target, anchor) {

      insert(target, div2, anchor);

      append(div2, div1);

      append(div1, t0);

      append(div1, t1);

      append(div1, div0);

      append(div2, t2);

      current = true;

    },

    p(ctx2, dirty) {

      if ((!current || dirty & 1) && t0_value !== (t0_value = ctx2[1].name + ""))

        set_data(t0, t0_value);

    },

    i(local) {

      if (current)

        return;

      add_render_callback(() => {

        if (!div2_transition)

          div2_transition = create_bidirectional_transition(div2, fly, { x: 20 }, true);

        div2_transition.run(1);

      });

      current = true;

    },

    o(local) {

      if (!div2_transition)

        div2_transition = create_bidirectional_transition(div2, fly, { x: 20 }, false);

      div2_transition.run(0);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(div2);

      if (detaching && div2_transition)

        div2_transition.end();

    }

  };

}

function create_fragment$c(ctx) {

  let if_block_anchor;

  let current;

  let if_block = ctx[0].length > 0 && create_if_block$5(ctx);

  return {

    c() {

      if (if_block)

        if_block.c();

      if_block_anchor = empty();

    },

    m(target, anchor) {

      if (if_block)

        if_block.m(target, anchor);

      insert(target, if_block_anchor, anchor);

      current = true;

    },

    p(ctx2, [dirty]) {

      if (ctx2[0].length > 0) {

        if (if_block) {

          if_block.p(ctx2, dirty);

          if (dirty & 1) {

            transition_in(if_block, 1);

          }

        } else {

          if_block = create_if_block$5(ctx2);

          if_block.c();

          transition_in(if_block, 1);

          if_block.m(if_block_anchor.parentNode, if_block_anchor);

        }

      } else if (if_block) {

        group_outros();

        transition_out(if_block, 1, 1, () => {

          if_block = null;

        });

        check_outros();

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if (if_block)

        if_block.d(detaching);

      if (detaching)

        detach(if_block_anchor);

    }

  };

}

function instance$b($$self, $$props, $$invalidate) {

  let $uploads;

  component_subscribe($$self, uploads, ($$value) => $$invalidate(0, $uploads = $$value));

  return [$uploads];

}

class UploadProgress extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$b, create_fragment$c, safe_not_equal, {});

  }

}

function create_fragment$b(ctx) {

  let svg;

  let rect;

  let path;

  return {

    c() {

      svg = svg_element("svg");

      rect = svg_element("rect");

      path = svg_element("path");

      attr(rect, "width", "16");

      attr(rect, "height", "16");

      attr(rect, "fill", "white");

      attr(path, "fill-rule", "evenodd");

      attr(path, "clip-rule", "evenodd");

      attr(path, "d", "M1 0H15C15.6 0 16 0.4 16 1V15C16 15.6 15.6 16 15 16H1C0.4 16 0 15.6 0 15V1C0 0.4 0.4 0 1 0ZM10.1 11.5L11.5 10.1L9.4 8L11.5 5.9L10.1 4.5L8 6.6L5.9 4.5L4.5 5.9L6.6 8L4.5 10.1L5.9 11.5L8 9.4L10.1 11.5Z");

      attr(path, "fill", "currentColor");

      attr(svg, "width", "16");

      attr(svg, "height", "16");

      attr(svg, "viewBox", "0 0 16 16");

      attr(svg, "fill", "none");

      attr(svg, "xmlns", "http://www.w3.org/2000/svg");

    },

    m(target, anchor) {

      insert(target, svg, anchor);

      append(svg, rect);

      append(svg, path);

    },

    p: noop,

    i: noop,

    o: noop,

    d(detaching) {

      if (detaching)

        detach(svg);

    }

  };

}

class IconDelete extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, null, create_fragment$b, safe_not_equal, {});

  }

}

function create_fragment$a(ctx) {

  let svg;

  let path0;

  let path1;

  let path2;

  return {

    c() {

      svg = svg_element("svg");

      path0 = svg_element("path");

      path1 = svg_element("path");

      path2 = svg_element("path");

      attr(path0, "d", "M7 8H1c-.6 0-1 .4-1 1v6c0 .6.4 1 1 1h6c.6 0 1-.4 1-1V9c0-.6-.4-1-1-1z");

      attr(path0, "fill", "currentColor");

      attr(path1, "d", "M11 4H2v2h8v8h2V5c0-.6-.4-1-1-1z");

      attr(path1, "fill", "currentColor");

      attr(path2, "d", "M15 0H6v2h8v8h2V1c0-.6-.4-1-1-1z");

      attr(path2, "fill", "currentColor");

      attr(svg, "fill", "none");

      attr(svg, "xmlns", "http://www.w3.org/2000/svg");

      attr(svg, "width", ctx[0]);

      attr(svg, "height", ctx[0]);

      attr(svg, "viewBox", "0 0 16 16");

    },

    m(target, anchor) {

      insert(target, svg, anchor);

      append(svg, path0);

      append(svg, path1);

      append(svg, path2);

    },

    p(ctx2, [dirty]) {

      if (dirty & 1) {

        attr(svg, "width", ctx2[0]);

      }

      if (dirty & 1) {

        attr(svg, "height", ctx2[0]);

      }

    },

    i: noop,

    o: noop,

    d(detaching) {

      if (detaching)

        detach(svg);

    }

  };

}

function instance$a($$self, $$props, $$invalidate) {

  let { size = 16 } = $$props;

  $$self.$$set = ($$props2) => {

    if ("size" in $$props2)

      $$invalidate(0, size = $$props2.size);

  };

  return [size];

}

class IconCopy extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$a, create_fragment$a, safe_not_equal, { size: 0 });

  }

}

function useFileActions(file, element2) {

  const queryClient = useQueryClient();

  const options = getOptions();

  const handleDelete = () => {

    if (!confirm(t("deleteConfirm"))) {

      return;

    }

    removeFile(options, queryClient, file);

  };

  const handleClick = () => {

    element2.dispatchEvent(new CustomEvent("selectfile", { detail: file, bubbles: true }));

  };

  const handleCopy = () => {

    navigator.clipboard.writeText(file.url);

    flash("Le lien a \xE9t\xE9 copi\xE9 dans votre presse papier");

  };

  return {

    handleClick,

    handleCopy,

    handleDelete

  };

}

function shorten(str, max) {

  if (str.length <= max) {

    return str;

  }

  return str.slice(0, max - 11) + "..." + str.slice(-8);

}

var FileRow_svelte_svelte_type_style_lang = "";

function create_if_block$4(ctx) {

  let button;

  let icondelete;

  let tooltip_action;

  let current;

  let mounted;

  let dispose;

  icondelete = new IconDelete({});

  return {

    c() {

      button = element("button");

      create_component(icondelete.$$.fragment);

      attr(button, "class", "svelte-1ntuvox");

    },

    m(target, anchor) {

      insert(target, button, anchor);

      mount_component(icondelete, button, null);

      current = true;

      if (!mounted) {

        dispose = [

          action_destroyer(tooltip_action = tooltip.call(null, button, t("delete"))),

          listen(button, "click", stop_propagation(prevent_default(function() {

            if (is_function(ctx[3].handleDelete))

              ctx[3].handleDelete.apply(this, arguments);

          })))

        ];

        mounted = true;

      }

    },

    p(new_ctx, dirty) {

      ctx = new_ctx;

    },

    i(local) {

      if (current)

        return;

      transition_in(icondelete.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(icondelete.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(button);

      destroy_component(icondelete);

      mounted = false;

      run_all(dispose);

    }

  };

}

function create_fragment$9(ctx) {

  let tr;

  let td0;

  let t0;

  let td1;

  let img;

  let img_src_value;

  let t1;

  let td2;

  let t2;

  let t3;

  let td3;

  let t4_value = (ctx[0].size ? ctx[5].format(ctx[0].size / 1e3) : null) + "";

  let t4;

  let t5;

  let td4;

  let button;

  let iconcopy;

  let t6;

  let current;

  let mounted;

  let dispose;

  iconcopy = new IconCopy({});

  let if_block = !ctx[4].readOnly && create_if_block$4(ctx);

  return {

    c() {

      tr = element("tr");

      td0 = element("td");

      t0 = space();

      td1 = element("td");

      img = element("img");

      t1 = space();

      td2 = element("td");

      t2 = text(ctx[2]);

      t3 = space();

      td3 = element("td");

      t4 = text(t4_value);

      t5 = space();

      td4 = element("td");

      button = element("button");

      create_component(iconcopy.$$.fragment);

      t6 = space();

      if (if_block)

        if_block.c();

      attr(td0, "class", "svelte-1ntuvox");

      if (!src_url_equal(img.src, img_src_value = ctx[0].thumbnail))

        attr(img, "src", img_src_value);

      attr(img, "alt", "");

      attr(img, "class", "svelte-1ntuvox");

      attr(td1, "class", "svelte-1ntuvox");

      attr(td2, "class", "filename svelte-1ntuvox");

      attr(td3, "class", "svelte-1ntuvox");

      attr(button, "class", "svelte-1ntuvox");

      attr(td4, "class", "actions svelte-1ntuvox");

      attr(tr, "class", "svelte-1ntuvox");

    },

    m(target, anchor) {

      insert(target, tr, anchor);

      append(tr, td0);

      append(tr, t0);

      append(tr, td1);

      append(td1, img);

      append(tr, t1);

      append(tr, td2);

      append(td2, t2);

      append(tr, t3);

      append(tr, td3);

      append(td3, t4);

      append(tr, t5);

      append(tr, td4);

      append(td4, button);

      mount_component(iconcopy, button, null);

      append(td4, t6);

      if (if_block)

        if_block.m(td4, null);

      ctx[6](tr);

      current = true;

      if (!mounted) {

        dispose = [

          action_destroyer(tooltip.call(null, button, t("copy"))),

          listen(button, "click", stop_propagation(prevent_default(function() {

            if (is_function(ctx[3].handleCopy))

              ctx[3].handleCopy.apply(this, arguments);

          }))),

          listen(tr, "click", function() {

            if (is_function(ctx[3].handleClick))

              ctx[3].handleClick.apply(this, arguments);

          })

        ];

        mounted = true;

      }

    },

    p(new_ctx, [dirty]) {

      ctx = new_ctx;

      if (!current || dirty & 1 && !src_url_equal(img.src, img_src_value = ctx[0].thumbnail)) {

        attr(img, "src", img_src_value);

      }

      if (!current || dirty & 4)

        set_data(t2, ctx[2]);

      if ((!current || dirty & 1) && t4_value !== (t4_value = (ctx[0].size ? ctx[5].format(ctx[0].size / 1e3) : null) + ""))

        set_data(t4, t4_value);

      if (!ctx[4].readOnly)

        if_block.p(ctx, dirty);

    },

    i(local) {

      if (current)

        return;

      transition_in(iconcopy.$$.fragment, local);

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(iconcopy.$$.fragment, local);

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(tr);

      destroy_component(iconcopy);

      if (if_block)

        if_block.d();

      ctx[6](null);

      mounted = false;

      run_all(dispose);

    }

  };

}

function instance$9($$self, $$props, $$invalidate) {

  let actions;

  let filename;

  let row;

  const options = getOptions();

  const sizeFormatter = new Intl.NumberFormat(void 0, {

    style: "unit",

    unit: "kilobyte",

    unitDisplay: "short",

    maximumSignificantDigits: 3

  });

  let { file } = $$props;

  function tr_binding($$value) {

    binding_callbacks[$$value ? "unshift" : "push"](() => {

      row = $$value;

      $$invalidate(1, row);

    });

  }

  $$self.$$set = ($$props2) => {

    if ("file" in $$props2)

      $$invalidate(0, file = $$props2.file);

  };

  $$self.$$.update = () => {

    if ($$self.$$.dirty & 3) {

      $$invalidate(3, actions = useFileActions(file, row));

    }

    if ($$self.$$.dirty & 1) {

      $$invalidate(2, filename = shorten(file.name, 35));

    }

  };

  return [file, row, filename, actions, options, sizeFormatter, tr_binding];

}

class FileRow extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$9, create_fragment$9, safe_not_equal, { file: 0 });

  }

}

var FilesListRows_svelte_svelte_type_style_lang = "";

function get_each_context$2(ctx, list, i) {

  const child_ctx = ctx.slice();

  child_ctx[1] = list[i];

  return child_ctx;

}

function create_each_block$2(ctx) {

  let filerow;

  let current;

  filerow = new FileRow({ props: { file: ctx[1] } });

  return {

    c() {

      create_component(filerow.$$.fragment);

    },

    m(target, anchor) {

      mount_component(filerow, target, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      const filerow_changes = {};

      if (dirty & 1)

        filerow_changes.file = ctx2[1];

      filerow.$set(filerow_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(filerow.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(filerow.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(filerow, detaching);

    }

  };

}

function create_fragment$8(ctx) {

  let table;

  let thead;

  let tr;

  let th0;

  let t0;

  let th1;

  let t1;

  let th2;

  let t3;

  let th3;

  let t5;

  let th4;

  let t6;

  let tbody;

  let current;

  let each_value = ctx[0];

  let each_blocks = [];

  for (let i = 0; i < each_value.length; i += 1) {

    each_blocks[i] = create_each_block$2(get_each_context$2(ctx, each_value, i));

  }

  const out = (i) => transition_out(each_blocks[i], 1, 1, () => {

    each_blocks[i] = null;

  });

  return {

    c() {

      table = element("table");

      thead = element("thead");

      tr = element("tr");

      th0 = element("th");

      t0 = space();

      th1 = element("th");

      t1 = space();

      th2 = element("th");

      th2.textContent = `${t("filename")}`;

      t3 = space();

      th3 = element("th");

      th3.textContent = `${t("size")}`;

      t5 = space();

      th4 = element("th");

      t6 = space();

      tbody = element("tbody");

      for (let i = 0; i < each_blocks.length; i += 1) {

        each_blocks[i].c();

      }

      attr(table, "class", "svelte-88gfvn");

    },

    m(target, anchor) {

      insert(target, table, anchor);

      append(table, thead);

      append(thead, tr);

      append(tr, th0);

      append(tr, t0);

      append(tr, th1);

      append(tr, t1);

      append(tr, th2);

      append(tr, t3);

      append(tr, th3);

      append(tr, t5);

      append(tr, th4);

      append(table, t6);

      append(table, tbody);

      for (let i = 0; i < each_blocks.length; i += 1) {

        each_blocks[i].m(tbody, null);

      }

      current = true;

    },

    p(ctx2, [dirty]) {

      if (dirty & 1) {

        each_value = ctx2[0];

        let i;

        for (i = 0; i < each_value.length; i += 1) {

          const child_ctx = get_each_context$2(ctx2, each_value, i);

          if (each_blocks[i]) {

            each_blocks[i].p(child_ctx, dirty);

            transition_in(each_blocks[i], 1);

          } else {

            each_blocks[i] = create_each_block$2(child_ctx);

            each_blocks[i].c();

            transition_in(each_blocks[i], 1);

            each_blocks[i].m(tbody, null);

          }

        }

        group_outros();

        for (i = each_value.length; i < each_blocks.length; i += 1) {

          out(i);

        }

        check_outros();

      }

    },

    i(local) {

      if (current)

        return;

      for (let i = 0; i < each_value.length; i += 1) {

        transition_in(each_blocks[i]);

      }

      current = true;

    },

    o(local) {

      each_blocks = each_blocks.filter(Boolean);

      for (let i = 0; i < each_blocks.length; i += 1) {

        transition_out(each_blocks[i]);

      }

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(table);

      destroy_each(each_blocks, detaching);

    }

  };

}

function instance$8($$self, $$props, $$invalidate) {

  let { files } = $$props;

  $$self.$$set = ($$props2) => {

    if ("files" in $$props2)

      $$invalidate(0, files = $$props2.files);

  };

  return [files];

}

class FilesListRows extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$8, create_fragment$8, safe_not_equal, { files: 0 });

  }

}

var FileCell_svelte_svelte_type_style_lang = "";

function create_if_block$3(ctx) {

  let button;

  let icondelete;

  let tooltip_action;

  let current;

  let mounted;

  let dispose;

  icondelete = new IconDelete({});

  return {

    c() {

      button = element("button");

      create_component(icondelete.$$.fragment);

      attr(button, "class", "fm-delete svelte-c91skb");

    },

    m(target, anchor) {

      insert(target, button, anchor);

      mount_component(icondelete, button, null);

      current = true;

      if (!mounted) {

        dispose = [

          action_destroyer(tooltip_action = tooltip.call(null, button, t("delete"))),

          listen(button, "click", stop_propagation(prevent_default(function() {

            if (is_function(ctx[2].handleDelete))

              ctx[2].handleDelete.apply(this, arguments);

          })))

        ];

        mounted = true;

      }

    },

    p(new_ctx, dirty) {

      ctx = new_ctx;

    },

    i(local) {

      if (current)

        return;

      transition_in(icondelete.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(icondelete.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(button);

      destroy_component(icondelete);

      mounted = false;

      run_all(dispose);

    }

  };

}

function create_fragment$7(ctx) {

  let div2;

  let div0;

  let img;

  let img_src_value;

  let t0;

  let t1;

  let div1;

  let t2;

  let current;

  let mounted;

  let dispose;

  let if_block = !ctx[4].readOnly && create_if_block$3(ctx);

  return {

    c() {

      div2 = element("div");

      div0 = element("div");

      img = element("img");

      t0 = space();

      if (if_block)

        if_block.c();

      t1 = space();

      div1 = element("div");

      t2 = text(ctx[3]);

      if (!src_url_equal(img.src, img_src_value = ctx[0].thumbnail))

        attr(img, "src", img_src_value);

      attr(img, "alt", "");

      attr(img, "class", "svelte-c91skb");

      attr(div0, "class", "fm-thumbnail svelte-c91skb");

      attr(div1, "class", "fm-filename svelte-c91skb");

      attr(div2, "class", "fm-file svelte-c91skb");

    },

    m(target, anchor) {

      insert(target, div2, anchor);

      append(div2, div0);

      append(div0, img);

      append(div0, t0);

      if (if_block)

        if_block.m(div0, null);

      append(div2, t1);

      append(div2, div1);

      append(div1, t2);

      ctx[5](div2);

      current = true;

      if (!mounted) {

        dispose = listen(div2, "click", function() {

          if (is_function(ctx[2].handleClick))

            ctx[2].handleClick.apply(this, arguments);

        });

        mounted = true;

      }

    },

    p(new_ctx, [dirty]) {

      ctx = new_ctx;

      if (!current || dirty & 1 && !src_url_equal(img.src, img_src_value = ctx[0].thumbnail)) {

        attr(img, "src", img_src_value);

      }

      if (!ctx[4].readOnly)

        if_block.p(ctx, dirty);

      if (!current || dirty & 8)

        set_data(t2, ctx[3]);

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(div2);

      if (if_block)

        if_block.d();

      ctx[5](null);

      mounted = false;

      dispose();

    }

  };

}

function instance$7($$self, $$props, $$invalidate) {

  let filename;

  let actions;

  let el;

  const options = getOptions();

  let { file } = $$props;

  function div2_binding($$value) {

    binding_callbacks[$$value ? "unshift" : "push"](() => {

      el = $$value;

      $$invalidate(1, el);

    });

  }

  $$self.$$set = ($$props2) => {

    if ("file" in $$props2)

      $$invalidate(0, file = $$props2.file);

  };

  $$self.$$.update = () => {

    if ($$self.$$.dirty & 1) {

      $$invalidate(3, filename = shorten(file.name, 30));

    }

    if ($$self.$$.dirty & 3) {

      $$invalidate(2, actions = useFileActions(file, el));

    }

  };

  return [file, el, actions, filename, options, div2_binding];

}

class FileCell extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$7, create_fragment$7, safe_not_equal, { file: 0 });

  }

}

var FilesListGrid_svelte_svelte_type_style_lang = "";

function get_each_context$1(ctx, list, i) {

  const child_ctx = ctx.slice();

  child_ctx[1] = list[i];

  return child_ctx;

}

function create_each_block$1(ctx) {

  let filecell;

  let current;

  filecell = new FileCell({ props: { file: ctx[1] } });

  return {

    c() {

      create_component(filecell.$$.fragment);

    },

    m(target, anchor) {

      mount_component(filecell, target, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      const filecell_changes = {};

      if (dirty & 1)

        filecell_changes.file = ctx2[1];

      filecell.$set(filecell_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(filecell.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(filecell.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(filecell, detaching);

    }

  };

}

function create_fragment$6(ctx) {

  let div;

  let current;

  let each_value = ctx[0];

  let each_blocks = [];

  for (let i = 0; i < each_value.length; i += 1) {

    each_blocks[i] = create_each_block$1(get_each_context$1(ctx, each_value, i));

  }

  const out = (i) => transition_out(each_blocks[i], 1, 1, () => {

    each_blocks[i] = null;

  });

  return {

    c() {

      div = element("div");

      for (let i = 0; i < each_blocks.length; i += 1) {

        each_blocks[i].c();

      }

      attr(div, "class", "grid svelte-trks37");

    },

    m(target, anchor) {

      insert(target, div, anchor);

      for (let i = 0; i < each_blocks.length; i += 1) {

        each_blocks[i].m(div, null);

      }

      current = true;

    },

    p(ctx2, [dirty]) {

      if (dirty & 1) {

        each_value = ctx2[0];

        let i;

        for (i = 0; i < each_value.length; i += 1) {

          const child_ctx = get_each_context$1(ctx2, each_value, i);

          if (each_blocks[i]) {

            each_blocks[i].p(child_ctx, dirty);

            transition_in(each_blocks[i], 1);

          } else {

            each_blocks[i] = create_each_block$1(child_ctx);

            each_blocks[i].c();

            transition_in(each_blocks[i], 1);

            each_blocks[i].m(div, null);

          }

        }

        group_outros();

        for (i = each_value.length; i < each_blocks.length; i += 1) {

          out(i);

        }

        check_outros();

      }

    },

    i(local) {

      if (current)

        return;

      for (let i = 0; i < each_value.length; i += 1) {

        transition_in(each_blocks[i]);

      }

      current = true;

    },

    o(local) {

      each_blocks = each_blocks.filter(Boolean);

      for (let i = 0; i < each_blocks.length; i += 1) {

        transition_out(each_blocks[i]);

      }

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(div);

      destroy_each(each_blocks, detaching);

    }

  };

}

function instance$6($$self, $$props, $$invalidate) {

  let { files } = $$props;

  $$self.$$set = ($$props2) => {

    if ("files" in $$props2)

      $$invalidate(0, files = $$props2.files);

  };

  return [files];

}

class FilesListGrid extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$6, create_fragment$6, safe_not_equal, { files: 0 });

  }

}

var FilesList_svelte_svelte_type_style_lang = "";

function create_else_block_1(ctx) {

  let div;

  let p0;

  let t1;

  let p1;

  let t3;

  let current;

  let if_block = ctx[3] && !ctx[5].readOnly && create_if_block_3(ctx);

  return {

    c() {

      div = element("div");

      p0 = element("p");

      p0.textContent = `${t("emptyTitle")}`;

      t1 = space();

      p1 = element("p");

      p1.textContent = `${t("emptyDescription")}`;

      t3 = space();

      if (if_block)

        if_block.c();

      attr(p0, "class", "big svelte-mbr02");

      attr(p1, "class", "svelte-mbr02");

      attr(div, "class", "empty svelte-mbr02");

    },

    m(target, anchor) {

      insert(target, div, anchor);

      append(div, p0);

      append(div, t1);

      append(div, p1);

      append(div, t3);

      if (if_block)

        if_block.m(div, null);

      current = true;

    },

    p(ctx2, dirty) {

      if (ctx2[3] && !ctx2[5].readOnly) {

        if (if_block) {

          if_block.p(ctx2, dirty);

          if (dirty & 8) {

            transition_in(if_block, 1);

          }

        } else {

          if_block = create_if_block_3(ctx2);

          if_block.c();

          transition_in(if_block, 1);

          if_block.m(div, null);

        }

      } else if (if_block) {

        group_outros();

        transition_out(if_block, 1, 1, () => {

          if_block = null;

        });

        check_outros();

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(div);

      if (if_block)

        if_block.d();

    }

  };

}

function create_if_block_2(ctx) {

  let div;

  let iconloader;

  let current;

  iconloader = new IconLoader({});

  return {

    c() {

      div = element("div");

      create_component(iconloader.$$.fragment);

      attr(div, "class", "empty svelte-mbr02");

    },

    m(target, anchor) {

      insert(target, div, anchor);

      mount_component(iconloader, div, null);

      current = true;

    },

    p: noop,

    i(local) {

      if (current)

        return;

      transition_in(iconloader.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconloader.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(div);

      destroy_component(iconloader);

    }

  };

}

function create_if_block$2(ctx) {

  let current_block_type_index;

  let if_block;

  let if_block_anchor;

  let current;

  const if_block_creators = [create_if_block_1$1, create_else_block];

  const if_blocks = [];

  function select_block_type_1(ctx2, dirty) {

    if (ctx2[0] === "rows")

      return 0;

    return 1;

  }

  current_block_type_index = select_block_type_1(ctx);

  if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

  return {

    c() {

      if_block.c();

      if_block_anchor = empty();

    },

    m(target, anchor) {

      if_blocks[current_block_type_index].m(target, anchor);

      insert(target, if_block_anchor, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      let previous_block_index = current_block_type_index;

      current_block_type_index = select_block_type_1(ctx2);

      if (current_block_type_index === previous_block_index) {

        if_blocks[current_block_type_index].p(ctx2, dirty);

      } else {

        group_outros();

        transition_out(if_blocks[previous_block_index], 1, 1, () => {

          if_blocks[previous_block_index] = null;

        });

        check_outros();

        if_block = if_blocks[current_block_type_index];

        if (!if_block) {

          if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx2);

          if_block.c();

        } else {

          if_block.p(ctx2, dirty);

        }

        transition_in(if_block, 1);

        if_block.m(if_block_anchor.parentNode, if_block_anchor);

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if_blocks[current_block_type_index].d(detaching);

      if (detaching)

        detach(if_block_anchor);

    }

  };

}

function create_if_block_3(ctx) {

  let button;

  let t0;

  let t1_value = t("deleteFolder") + "";

  let t1;

  let button_disabled_value;

  let current;

  let mounted;

  let dispose;

  let if_block = ctx[4].isLoading && create_if_block_4();

  return {

    c() {

      button = element("button");

      if (if_block)

        if_block.c();

      t0 = space();

      t1 = text(t1_value);

      attr(button, "class", "delete-folder svelte-mbr02");

      button.disabled = button_disabled_value = ctx[4].isLoading;

    },

    m(target, anchor) {

      insert(target, button, anchor);

      if (if_block)

        if_block.m(button, null);

      append(button, t0);

      append(button, t1);

      current = true;

      if (!mounted) {

        dispose = listen(button, "click", prevent_default(ctx[7]));

        mounted = true;

      }

    },

    p(ctx2, dirty) {

      if (ctx2[4].isLoading) {

        if (if_block) {

          if (dirty & 16) {

            transition_in(if_block, 1);

          }

        } else {

          if_block = create_if_block_4();

          if_block.c();

          transition_in(if_block, 1);

          if_block.m(button, t0);

        }

      } else if (if_block) {

        group_outros();

        transition_out(if_block, 1, 1, () => {

          if_block = null;

        });

        check_outros();

      }

      if (!current || dirty & 16 && button_disabled_value !== (button_disabled_value = ctx2[4].isLoading)) {

        button.disabled = button_disabled_value;

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(button);

      if (if_block)

        if_block.d();

      mounted = false;

      dispose();

    }

  };

}

function create_if_block_4(ctx) {

  let iconloader;

  let current;

  iconloader = new IconLoader({ props: { size: 12 } });

  return {

    c() {

      create_component(iconloader.$$.fragment);

    },

    m(target, anchor) {

      mount_component(iconloader, target, anchor);

      current = true;

    },

    i(local) {

      if (current)

        return;

      transition_in(iconloader.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconloader.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(iconloader, detaching);

    }

  };

}

function create_else_block(ctx) {

  let fileslistgrid;

  let current;

  fileslistgrid = new FilesListGrid({ props: { files: ctx[2] } });

  return {

    c() {

      create_component(fileslistgrid.$$.fragment);

    },

    m(target, anchor) {

      mount_component(fileslistgrid, target, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      const fileslistgrid_changes = {};

      if (dirty & 4)

        fileslistgrid_changes.files = ctx2[2];

      fileslistgrid.$set(fileslistgrid_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(fileslistgrid.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(fileslistgrid.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(fileslistgrid, detaching);

    }

  };

}

function create_if_block_1$1(ctx) {

  let fileslistrows;

  let current;

  fileslistrows = new FilesListRows({ props: { files: ctx[2] } });

  return {

    c() {

      create_component(fileslistrows.$$.fragment);

    },

    m(target, anchor) {

      mount_component(fileslistrows, target, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      const fileslistrows_changes = {};

      if (dirty & 4)

        fileslistrows_changes.files = ctx2[2];

      fileslistrows.$set(fileslistrows_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(fileslistrows.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(fileslistrows.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(fileslistrows, detaching);

    }

  };

}

function create_fragment$5(ctx) {

  let current_block_type_index;

  let if_block;

  let if_block_anchor;

  let current;

  const if_block_creators = [create_if_block$2, create_if_block_2, create_else_block_1];

  const if_blocks = [];

  function select_block_type(ctx2, dirty) {

    if (ctx2[2].length > 0)

      return 0;

    if (ctx2[1].isLoading)

      return 1;

    return 2;

  }

  current_block_type_index = select_block_type(ctx);

  if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

  return {

    c() {

      if_block.c();

      if_block_anchor = empty();

    },

    m(target, anchor) {

      if_blocks[current_block_type_index].m(target, anchor);

      insert(target, if_block_anchor, anchor);

      current = true;

    },

    p(ctx2, [dirty]) {

      let previous_block_index = current_block_type_index;

      current_block_type_index = select_block_type(ctx2);

      if (current_block_type_index === previous_block_index) {

        if_blocks[current_block_type_index].p(ctx2, dirty);

      } else {

        group_outros();

        transition_out(if_blocks[previous_block_index], 1, 1, () => {

          if_blocks[previous_block_index] = null;

        });

        check_outros();

        if_block = if_blocks[current_block_type_index];

        if (!if_block) {

          if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx2);

          if_block.c();

        } else {

          if_block.p(ctx2, dirty);

        }

        transition_in(if_block, 1);

        if_block.m(if_block_anchor.parentNode, if_block_anchor);

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if_blocks[current_block_type_index].d(detaching);

      if (detaching)

        detach(if_block_anchor);

    }

  };

}

function instance$5($$self, $$props, $$invalidate) {

  let isEmpty;

  let $filesQuery;

  let $folders;

  let $searchQuery;

  let $deleteFolder;

  component_subscribe($$self, searchQuery, ($$value) => $$invalidate(12, $searchQuery = $$value));

  let { layout } = $$props;

  let { folder: folder2 } = $$props;

  const options = getOptions();

  const deleteFolder = useDeleteFolderMutation();

  component_subscribe($$self, deleteFolder, (value) => $$invalidate(4, $deleteFolder = value));

  const handleDelete = () => {

    if (folder2) {

      $deleteFolder.mutate(folder2);

    }

  };

  const filesQuery = useQuery(filesQueryKey(folder2 == null ? void 0 : folder2.id), () => getOptions().getFiles(folder2));

  component_subscribe($$self, filesQuery, (value) => $$invalidate(1, $filesQuery = value));

  let files = [];

  const folders = useQuery(foldersQueryKey(folder2 == null ? void 0 : folder2.id), () => [], { enabled: false });

  component_subscribe($$self, folders, (value) => $$invalidate(11, $folders = value));

  $$self.$$set = ($$props2) => {

    if ("layout" in $$props2)

      $$invalidate(0, layout = $$props2.layout);

    if ("folder" in $$props2)

      $$invalidate(10, folder2 = $$props2.folder);

  };

  $$self.$$.update = () => {

    var _a, _b;

    if ($$self.$$.dirty & 4098) {

      {

        $$invalidate(2, files = $filesQuery.isSuccess ? $filesQuery.data.filter((f) => $searchQuery ? f.name.includes($searchQuery) : true) : []);

      }

    }

    if ($$self.$$.dirty & 3074) {

      $$invalidate(3, isEmpty = (folder2 == null ? void 0 : folder2.id) && ((folder2 == null ? void 0 : folder2.children) && folder2.children.length === 0 || $folders.isSuccess && ((_a = $folders == null ? void 0 : $folders.data) == null ? void 0 : _a.length) === 0) && $filesQuery.isSuccess && ((_b = $filesQuery == null ? void 0 : $filesQuery.data) == null ? void 0 : _b.length) === 0);

    }

  };

  return [

    layout,

    $filesQuery,

    files,

    isEmpty,

    $deleteFolder,

    options,

    deleteFolder,

    handleDelete,

    filesQuery,

    folders,

    folder2,

    $folders,

    $searchQuery

  ];

}

class FilesList extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$5, create_fragment$5, safe_not_equal, { layout: 0, folder: 10 });

  }

}

function create_fragment$4(ctx) {

  let svg;

  let path0;

  let path1;

  return {

    c() {

      svg = svg_element("svg");

      path0 = svg_element("path");

      path1 = svg_element("path");

      attr(path0, "stroke", "currentColor");

      attr(path0, "stroke-width", "2");

      attr(path0, "d", "M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z");

      attr(path1, "stroke", "currentColor");

      attr(path1, "stroke-linecap", "round");

      attr(path1, "stroke-width", "2");

      attr(path1, "d", "M12 16.5v.5m0-10v6-6Z");

      attr(svg, "xmlns", "http://www.w3.org/2000/svg");

      attr(svg, "fill", "none");

      attr(svg, "viewBox", "0 0 24 24");

      attr(svg, "width", ctx[0]);

      attr(svg, "height", ctx[0]);

    },

    m(target, anchor) {

      insert(target, svg, anchor);

      append(svg, path0);

      append(svg, path1);

    },

    p(ctx2, [dirty]) {

      if (dirty & 1) {

        attr(svg, "width", ctx2[0]);

      }

      if (dirty & 1) {

        attr(svg, "height", ctx2[0]);

      }

    },

    i: noop,

    o: noop,

    d(detaching) {

      if (detaching)

        detach(svg);

    }

  };

}

function instance$4($$self, $$props, $$invalidate) {

  let { size = 24 } = $$props;

  $$self.$$set = ($$props2) => {

    if ("size" in $$props2)

      $$invalidate(0, size = $$props2.size);

  };

  return [size];

}

class IconCircleExclamation extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$4, create_fragment$4, safe_not_equal, { size: 0 });

  }

}

function create_fragment$3(ctx) {

  let svg;

  let path0;

  let path1;

  return {

    c() {

      svg = svg_element("svg");

      path0 = svg_element("path");

      path1 = svg_element("path");

      attr(path0, "stroke", "currentColor");

      attr(path0, "stroke-linecap", "round");

      attr(path0, "stroke-linejoin", "round");

      attr(path0, "stroke-width", "2");

      attr(path0, "d", "m8 12.5 3 3 5-6");

      attr(path1, "stroke", "currentColor");

      attr(path1, "stroke-width", "2");

      attr(path1, "d", "M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z");

      attr(svg, "xmlns", "http://www.w3.org/2000/svg");

      attr(svg, "fill", "none");

      attr(svg, "viewBox", "0 0 24 24");

      attr(svg, "width", ctx[0]);

      attr(svg, "height", ctx[0]);

    },

    m(target, anchor) {

      insert(target, svg, anchor);

      append(svg, path0);

      append(svg, path1);

    },

    p(ctx2, [dirty]) {

      if (dirty & 1) {

        attr(svg, "width", ctx2[0]);

      }

      if (dirty & 1) {

        attr(svg, "height", ctx2[0]);

      }

    },

    i: noop,

    o: noop,

    d(detaching) {

      if (detaching)

        detach(svg);

    }

  };

}

function instance$3($$self, $$props, $$invalidate) {

  let { size = 24 } = $$props;

  $$self.$$set = ($$props2) => {

    if ("size" in $$props2)

      $$invalidate(0, size = $$props2.size);

  };

  return [size];

}

class IconCircleCheck extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$3, create_fragment$3, safe_not_equal, { size: 0 });

  }

}

var Alert_svelte_svelte_type_style_lang = "";

function create_if_block_1(ctx) {

  let iconcirclecheck;

  let t2;

  let div;

  let current;

  iconcirclecheck = new IconCircleCheck({});

  return {

    c() {

      create_component(iconcirclecheck.$$.fragment);

      t2 = space();

      div = element("div");

      attr(div, "class", "fm-progress svelte-rcwbts");

    },

    m(target, anchor) {

      mount_component(iconcirclecheck, target, anchor);

      insert(target, t2, anchor);

      insert(target, div, anchor);

      current = true;

    },

    i(local) {

      if (current)

        return;

      transition_in(iconcirclecheck.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconcirclecheck.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(iconcirclecheck, detaching);

      if (detaching)

        detach(t2);

      if (detaching)

        detach(div);

    }

  };

}

function create_if_block$1(ctx) {

  let iconcircleexclamation;

  let current;

  iconcircleexclamation = new IconCircleExclamation({});

  return {

    c() {

      create_component(iconcircleexclamation.$$.fragment);

    },

    m(target, anchor) {

      mount_component(iconcircleexclamation, target, anchor);

      current = true;

    },

    i(local) {

      if (current)

        return;

      transition_in(iconcircleexclamation.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(iconcircleexclamation.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(iconcircleexclamation, detaching);

    }

  };

}

function create_fragment$2(ctx) {

  let div;

  let current_block_type_index;

  let if_block;

  let t0;

  let t1_value = ctx[0].message + "";

  let t1;

  let t2;

  let button;

  let current;

  let mounted;

  let dispose;

  const if_block_creators = [create_if_block$1, create_if_block_1];

  const if_blocks = [];

  function select_block_type(ctx2, dirty) {

    if (ctx2[0].type === "danger")

      return 0;

    if (ctx2[0].type === "success")

      return 1;

    return -1;

  }

  if (~(current_block_type_index = select_block_type(ctx))) {

    if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);

  }

  return {

    c() {

      div = element("div");

      if (if_block)

        if_block.c();

      t0 = space();

      t1 = text(t1_value);

      t2 = space();

      button = element("button");

      button.textContent = "\xD7";

      attr(button, "class", "fm-close svelte-rcwbts");

      attr(div, "class", "fm-alert svelte-rcwbts");

      toggle_class(div, "fm-danger", ctx[0].type === "danger");

      toggle_class(div, "fm-success", ctx[0].type === "success");

    },

    m(target, anchor) {

      insert(target, div, anchor);

      if (~current_block_type_index) {

        if_blocks[current_block_type_index].m(div, null);

      }

      append(div, t0);

      append(div, t1);

      append(div, t2);

      append(div, button);

      current = true;

      if (!mounted) {

        dispose = listen(button, "click", prevent_default(ctx[1]));

        mounted = true;

      }

    },

    p(ctx2, [dirty]) {

      let previous_block_index = current_block_type_index;

      current_block_type_index = select_block_type(ctx2);

      if (current_block_type_index !== previous_block_index) {

        if (if_block) {

          group_outros();

          transition_out(if_blocks[previous_block_index], 1, 1, () => {

            if_blocks[previous_block_index] = null;

          });

          check_outros();

        }

        if (~current_block_type_index) {

          if_block = if_blocks[current_block_type_index];

          if (!if_block) {

            if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx2);

            if_block.c();

          }

          transition_in(if_block, 1);

          if_block.m(div, t0);

        } else {

          if_block = null;

        }

      }

      if ((!current || dirty & 1) && t1_value !== (t1_value = ctx2[0].message + ""))

        set_data(t1, t1_value);

      if (dirty & 1) {

        toggle_class(div, "fm-danger", ctx2[0].type === "danger");

      }

      if (dirty & 1) {

        toggle_class(div, "fm-success", ctx2[0].type === "success");

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(div);

      if (~current_block_type_index) {

        if_blocks[current_block_type_index].d();

      }

      mounted = false;

      dispose();

    }

  };

}

function instance$2($$self, $$props, $$invalidate) {

  let { message } = $$props;

  const handleClose = () => {

    deleteFlashMessage(message.id);

  };

  $$self.$$set = ($$props2) => {

    if ("message" in $$props2)

      $$invalidate(0, message = $$props2.message);

  };

  return [message, handleClose];

}

class Alert extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$2, create_fragment$2, safe_not_equal, { message: 0 });

  }

}

var Alerts_svelte_svelte_type_style_lang = "";

function get_each_context(ctx, list, i) {

  const child_ctx = ctx.slice();

  child_ctx[1] = list[i];

  return child_ctx;

}

function create_each_block(ctx) {

  let div;

  let alert;

  let t2;

  let div_transition;

  let current;

  alert = new Alert({ props: { message: ctx[1] } });

  return {

    c() {

      div = element("div");

      create_component(alert.$$.fragment);

      t2 = space();

    },

    m(target, anchor) {

      insert(target, div, anchor);

      mount_component(alert, div, null);

      append(div, t2);

      current = true;

    },

    p(ctx2, dirty) {

      const alert_changes = {};

      if (dirty & 1)

        alert_changes.message = ctx2[1];

      alert.$set(alert_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(alert.$$.fragment, local);

      add_render_callback(() => {

        if (!div_transition)

          div_transition = create_bidirectional_transition(div, fly, { x: 20 }, true);

        div_transition.run(1);

      });

      current = true;

    },

    o(local) {

      transition_out(alert.$$.fragment, local);

      if (!div_transition)

        div_transition = create_bidirectional_transition(div, fly, { x: 20 }, false);

      div_transition.run(0);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(div);

      destroy_component(alert);

      if (detaching && div_transition)

        div_transition.end();

    }

  };

}

function create_fragment$1(ctx) {

  let div;

  let current;

  let each_value = ctx[0];

  let each_blocks = [];

  for (let i = 0; i < each_value.length; i += 1) {

    each_blocks[i] = create_each_block(get_each_context(ctx, each_value, i));

  }

  const out = (i) => transition_out(each_blocks[i], 1, 1, () => {

    each_blocks[i] = null;

  });

  return {

    c() {

      div = element("div");

      for (let i = 0; i < each_blocks.length; i += 1) {

        each_blocks[i].c();

      }

      attr(div, "class", "fm-wrapper svelte-1ja1s61");

    },

    m(target, anchor) {

      insert(target, div, anchor);

      for (let i = 0; i < each_blocks.length; i += 1) {

        each_blocks[i].m(div, null);

      }

      current = true;

    },

    p(ctx2, [dirty]) {

      if (dirty & 1) {

        each_value = ctx2[0];

        let i;

        for (i = 0; i < each_value.length; i += 1) {

          const child_ctx = get_each_context(ctx2, each_value, i);

          if (each_blocks[i]) {

            each_blocks[i].p(child_ctx, dirty);

            transition_in(each_blocks[i], 1);

          } else {

            each_blocks[i] = create_each_block(child_ctx);

            each_blocks[i].c();

            transition_in(each_blocks[i], 1);

            each_blocks[i].m(div, null);

          }

        }

        group_outros();

        for (i = each_value.length; i < each_blocks.length; i += 1) {

          out(i);

        }

        check_outros();

      }

    },

    i(local) {

      if (current)

        return;

      for (let i = 0; i < each_value.length; i += 1) {

        transition_in(each_blocks[i]);

      }

      current = true;

    },

    o(local) {

      each_blocks = each_blocks.filter(Boolean);

      for (let i = 0; i < each_blocks.length; i += 1) {

        transition_out(each_blocks[i]);

      }

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(div);

      destroy_each(each_blocks, detaching);

    }

  };

}

function instance$1($$self, $$props, $$invalidate) {

  let $flashMessages;

  component_subscribe($$self, flashMessages, ($$value) => $$invalidate(0, $flashMessages = $$value));

  return [$flashMessages];

}

class Alerts extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance$1, create_fragment$1, safe_not_equal, {});

  }

}

var FileManager_svelte_svelte_type_style_lang = "";

function create_if_block(ctx) {

  let div2;

  let div1;

  let div0;

  let sidebar;

  let t0;

  let dropzone;

  let t1;

  let alerts;

  let t2;

  let uploadprogress;

  let clickOutside_action;

  let div0_transition;

  let div1_transition;

  let current;

  let mounted;

  let dispose;

  sidebar = new Sidebar({

    props: { lazyFolders: ctx[2] }

  });

  dropzone = new Dropzone({

    props: {

      $$slots: { default: [create_default_slot_1] },

      $$scope: { ctx }

    }

  });

  alerts = new Alerts({});

  uploadprogress = new UploadProgress({});

  return {

    c() {

      div2 = element("div");

      div1 = element("div");

      div0 = element("div");

      create_component(sidebar.$$.fragment);

      t0 = space();

      create_component(dropzone.$$.fragment);

      t1 = space();

      create_component(alerts.$$.fragment);

      t2 = space();

      create_component(uploadprogress.$$.fragment);

      attr(div0, "class", "fm-modal svelte-5hhtx0");

      attr(div1, "class", "fm-overlay svelte-5hhtx0");

      attr(div2, "class", "fm-root svelte-5hhtx0");

    },

    m(target, anchor) {

      insert(target, div2, anchor);

      append(div2, div1);

      append(div1, div0);

      mount_component(sidebar, div0, null);

      append(div0, t0);

      mount_component(dropzone, div0, null);

      append(div0, t1);

      mount_component(alerts, div0, null);

      append(div0, t2);

      mount_component(uploadprogress, div0, null);

      current = true;

      if (!mounted) {

        dispose = action_destroyer(clickOutside_action = clickOutside.call(null, div0, "close"));

        mounted = true;

      }

    },

    p(ctx2, dirty) {

      const sidebar_changes = {};

      if (dirty & 4)

        sidebar_changes.lazyFolders = ctx2[2];

      sidebar.$set(sidebar_changes);

      const dropzone_changes = {};

      if (dirty & 74) {

        dropzone_changes.$$scope = { dirty, ctx: ctx2 };

      }

      dropzone.$set(dropzone_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(sidebar.$$.fragment, local);

      transition_in(dropzone.$$.fragment, local);

      transition_in(alerts.$$.fragment, local);

      transition_in(uploadprogress.$$.fragment, local);

      add_render_callback(() => {

        if (!div0_transition)

          div0_transition = create_bidirectional_transition(div0, fly, { y: -30, duration: 500 }, true);

        div0_transition.run(1);

      });

      add_render_callback(() => {

        if (!div1_transition)

          div1_transition = create_bidirectional_transition(div1, fly, { duration: 300 }, true);

        div1_transition.run(1);

      });

      current = true;

    },

    o(local) {

      transition_out(sidebar.$$.fragment, local);

      transition_out(dropzone.$$.fragment, local);

      transition_out(alerts.$$.fragment, local);

      transition_out(uploadprogress.$$.fragment, local);

      if (!div0_transition)

        div0_transition = create_bidirectional_transition(div0, fly, { y: -30, duration: 500 }, false);

      div0_transition.run(0);

      if (!div1_transition)

        div1_transition = create_bidirectional_transition(div1, fly, { duration: 300 }, false);

      div1_transition.run(0);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(div2);

      destroy_component(sidebar);

      destroy_component(dropzone);

      destroy_component(alerts);

      destroy_component(uploadprogress);

      if (detaching && div0_transition)

        div0_transition.end();

      if (detaching && div1_transition)

        div1_transition.end();

      mounted = false;

      dispose();

    }

  };

}

function create_key_block(ctx) {

  let fileslist;

  let current;

  fileslist = new FilesList({

    props: {

      folder: ctx[3],

      layout: ctx[1]

    }

  });

  return {

    c() {

      create_component(fileslist.$$.fragment);

    },

    m(target, anchor) {

      mount_component(fileslist, target, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      const fileslist_changes = {};

      if (dirty & 8)

        fileslist_changes.folder = ctx2[3];

      if (dirty & 2)

        fileslist_changes.layout = ctx2[1];

      fileslist.$set(fileslist_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(fileslist.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(fileslist.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(fileslist, detaching);

    }

  };

}

function create_default_slot_1(ctx) {

  var _a;

  let previous_key = (_a = ctx[3]) == null ? void 0 : _a.id;

  let key_block_anchor;

  let current;

  let key_block = create_key_block(ctx);

  return {

    c() {

      key_block.c();

      key_block_anchor = empty();

    },

    m(target, anchor) {

      key_block.m(target, anchor);

      insert(target, key_block_anchor, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      var _a2;

      if (dirty & 8 && safe_not_equal(previous_key, previous_key = (_a2 = ctx2[3]) == null ? void 0 : _a2.id)) {

        group_outros();

        transition_out(key_block, 1, 1, noop);

        check_outros();

        key_block = create_key_block(ctx2);

        key_block.c();

        transition_in(key_block);

        key_block.m(key_block_anchor.parentNode, key_block_anchor);

      } else {

        key_block.p(ctx2, dirty);

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(key_block);

      current = true;

    },

    o(local) {

      transition_out(key_block);

      current = false;

    },

    d(detaching) {

      if (detaching)

        detach(key_block_anchor);

      key_block.d(detaching);

    }

  };

}

function create_default_slot(ctx) {

  let if_block_anchor;

  let current;

  let if_block = !ctx[0] && create_if_block(ctx);

  return {

    c() {

      if (if_block)

        if_block.c();

      if_block_anchor = empty();

    },

    m(target, anchor) {

      if (if_block)

        if_block.m(target, anchor);

      insert(target, if_block_anchor, anchor);

      current = true;

    },

    p(ctx2, dirty) {

      if (!ctx2[0]) {

        if (if_block) {

          if_block.p(ctx2, dirty);

          if (dirty & 1) {

            transition_in(if_block, 1);

          }

        } else {

          if_block = create_if_block(ctx2);

          if_block.c();

          transition_in(if_block, 1);

          if_block.m(if_block_anchor.parentNode, if_block_anchor);

        }

      } else if (if_block) {

        group_outros();

        transition_out(if_block, 1, 1, () => {

          if_block = null;

        });

        check_outros();

      }

    },

    i(local) {

      if (current)

        return;

      transition_in(if_block);

      current = true;

    },

    o(local) {

      transition_out(if_block);

      current = false;

    },

    d(detaching) {

      if (if_block)

        if_block.d(detaching);

      if (detaching)

        detach(if_block_anchor);

    }

  };

}

function create_fragment(ctx) {

  let queryclientprovider;

  let current;

  queryclientprovider = new QueryClientProvider({

    props: {

      client: ctx[4],

      $$slots: { default: [create_default_slot] },

      $$scope: { ctx }

    }

  });

  return {

    c() {

      create_component(queryclientprovider.$$.fragment);

    },

    m(target, anchor) {

      mount_component(queryclientprovider, target, anchor);

      current = true;

    },

    p(ctx2, [dirty]) {

      const queryclientprovider_changes = {};

      if (dirty & 79) {

        queryclientprovider_changes.$$scope = { dirty, ctx: ctx2 };

      }

      queryclientprovider.$set(queryclientprovider_changes);

    },

    i(local) {

      if (current)

        return;

      transition_in(queryclientprovider.$$.fragment, local);

      current = true;

    },

    o(local) {

      transition_out(queryclientprovider.$$.fragment, local);

      current = false;

    },

    d(detaching) {

      destroy_component(queryclientprovider, detaching);

    }

  };

}

function instance($$self, $$props, $$invalidate) {

  let $folder;

  component_subscribe($$self, folder, ($$value) => $$invalidate(3, $folder = $$value));

  let { hidden } = $$props;

  let { layout } = $$props;

  let { lazyFolders } = $$props;

  let { options } = $$props;

  setContext("options", options);

  const queryClient = new QueryClient();

  $$self.$$set = ($$props2) => {

    if ("hidden" in $$props2)

      $$invalidate(0, hidden = $$props2.hidden);

    if ("layout" in $$props2)

      $$invalidate(1, layout = $$props2.layout);

    if ("lazyFolders" in $$props2)

      $$invalidate(2, lazyFolders = $$props2.lazyFolders);

    if ("options" in $$props2)

      $$invalidate(5, options = $$props2.options);

  };

  return [hidden, layout, lazyFolders, $folder, queryClient, options];

}

class FileManager$1 extends SvelteComponent {

  constructor(options) {

    super();

    init(this, options, instance, create_fragment, safe_not_equal, {

      hidden: 0,

      layout: 1,

      lazyFolders: 2,

      options: 5

    });

  }

}

function objToQueryParams(o, p) {

  const params = p || new URLSearchParams();

  Object.keys(o).filter((k) => o[k] !== void 0).forEach((k) => params.set(k, o[k]));

  return params;

}

function fetchApi(baseUrl, path, options) {

  const o = __spreadValues({}, options);

  let url = new URL((baseUrl.startsWith("/") ? window.location.origin : "") + baseUrl);

  url.pathname = (url.pathname === "/" ? "" : url.pathname) + path;

  o.credentials = "include";

  o.headers = __spreadValues({}, o.headers);

  o.headers["Accept"] = "application/json";

  if (o.json) {

    o.body = JSON.stringify(o.json);

    o.headers["Content-Type"] = "application/json";

  }

  if (o.query) {

    objToQueryParams(o.query, url.searchParams);

  }

  if (o.params) {

    Object.keys(o.params).forEach((k) => url.pathname = url.pathname.replace(`%7B${k}%7D`, o.params[k]));

  }

  return fetch(url.toString(), o).then((r) => {

    if (r.status === HTTPStatus.NoContent) {

      return null;

    }

    if (r.status >= HTTPStatus.OK && r.status < HTTPStatus.MultipleChoices) {

      return r.json();

    }

    r.json().then((data) => {

      if (data == null ? void 0 : data.message) {

        flash(data.message, "danger");

      } else {

        flash(t("serverError"), "danger");

      }

    }).catch(() => flash(t("serverError"), "danger"));

    throw r;

  });

}

const config = {

  endpoint: "",

  readOnly: false,

  httpHeaders: {},

  getFolders(parent) {

    var _a;

    return fetchApi(this.endpoint, "/folders", {

      query: {

        parent: (_a = parent == null ? void 0 : parent.id) == null ? void 0 : _a.toString()

      },

      headers: this.httpHeaders

    });

  },

  createFolder(params) {

    return fetchApi(this.endpoint, "/folders", {

      method: "post",

      headers: this.httpHeaders,

      json: params

    });

  },

  deleteFolder(folder2) {

    return fetchApi(this.endpoint, "/folders/{id}", {

      method: "delete",

      headers: this.httpHeaders,

      params: {

        id: folder2.id.toString()

      }

    });

  },

  getFiles(folder2) {

    return fetchApi(this.endpoint, "/files", {

      headers: this.httpHeaders,

      query: {

        folder: (folder2 == null ? void 0 : folder2.id) ? folder2.id.toString() : void 0

      }

    });

  },

  uploadFile(file, folder2) {

    const form = new FormData();

    form.set("file", file);

    if (folder2 == null ? void 0 : folder2.id) {

      form.set("folder", folder2.id.toString());

    }

    return fetchApi(this.endpoint, "/files", {

      method: "post",

      headers: this.httpHeaders,

      body: form

    });

  },

  deleteFile(file) {

    return fetchApi(this.endpoint, `/files/{id}`, {

      method: "delete",

      headers: this.httpHeaders,

      params: {

        id: file.id.toString()

      }

    });

  }

};

const _FileManager = class {

  constructor(element2, options = {}) {

    __publicField(this, "fm", null);

    __publicField(this, "options");

    this.element = element2;

    this.options = __spreadValues(__spreadValues({}, config), options);

  }

  static get observedAttributes() {

    return ["hidden", "endpoint"];

  }

  connectedCallback() {

    this.element.style.setProperty("display", "block");

    const endpointAttr = this.element.getAttribute("endpoint");

    if (endpointAttr) {

      this.options.endpoint = endpointAttr;

    }

    this.options.readOnly = this.element.hasAttribute("readonly");

    if (!this.options.endpoint && !this.options.getFiles) {

      throw new Error("You must define an endpoint for this custom element");

    }

    setLang(document.documentElement.getAttribute("lang") || "en");

    this.fm = new FileManager$1({

      target: this.element,

      props: {

        hidden: this.element.hidden,

        layout: this.element.getAttribute("layout") || "grid",

        lazyFolders: this.element.hasAttribute("lazy-folders"),

        options: this.options

      }

    });

  }

  attributeChangedCallback(name, oldValue, newValue) {

    if (name === "hidden" && this.fm) {

      this.fm.$set({ hidden: newValue !== null });

    }

    if (name === "endpoint") {

      this.options.endpoint = newValue;

    }

  }

  disconnectedCallback() {

    var _a;

    (_a = this == null ? void 0 : this.fm) == null ? void 0 : _a.$destroy();

  }

  static register(name = "file-manager", options) {

    if (!this.registered.has(name)) {

      class AnonymousFileManager extends HTMLElement {

        constructor() {

          super();

          __publicField(this, "decorated");

          this.decorated = new _FileManager(this, options);

        }

        static get observedAttributes() {

          return _FileManager.observedAttributes;

        }

        connectedCallback() {

          return this.decorated.connectedCallback();

        }

        attributeChangedCallback(name2, oldValue, newValue) {

          return this.decorated.attributeChangedCallback(name2, oldValue, newValue);

        }

      }

      customElements.define(name, AnonymousFileManager);

      this.registered.set(name, true);

    }

  }

};

let FileManager = _FileManager;

__publicField(FileManager, "registered", /* @__PURE__ */ new Map());

export { FileManager };